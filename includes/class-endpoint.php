<?php

if( ! defined("EDD_HS::VERSION") ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * This class takes care of requests coming from HelpScout App Integrations
 */
class EDD_HS_Endpoint {

	/**
	 * @var string
	 */
	private $input = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->process();
	}

	/**
	 * Process the request
	 *  - Read input
	 *  - Validate signature
	 *  - Find purchase data
	 *  - Generate response
	 */
	private function process() {

		global $wpdb;

		$this->input = file_get_contents( 'php://input' );

		// check signature
		if( ! $this->is_signature_valid() ) {
			$this->respond( 'Invalid signature' );
		}

		$data = json_decode( $this->input, true );

		// if customer has more than one known email, perform an IN( email1, email 2) query
		if ( isset( $data['customer']['emails'] ) && is_array( $data['customer']['emails'] ) && count( $data['customer']['emails'] ) > 1 ) {
			$email_query = "IN (";
			foreach ( $data['customer']['emails'] as $email ) {
				$email_query .= "'" . $email . "',";
			}
			$email_query = rtrim( $email_query, ',' );
			$email_query .= ')';
		} else {
			$email_query = "= '" . $data['customer']['email'] . "'";
		}

		// query by email(s)
		$query   = "SELECT pm2.post_id, pm2.meta_value, p.post_status FROM $wpdb->postmeta pm, $wpdb->postmeta pm2, $wpdb->posts p WHERE pm.meta_key = '_edd_payment_user_email' AND pm.meta_value $email_query AND pm.post_id = pm2.post_id AND pm2.meta_key = '_edd_payment_meta' AND pm.post_id = p.ID ORDER BY pm.post_id DESC";
		$results = $wpdb->get_results( $query );

		if ( ! $results && ! empty( $data['customer']['fname'] ) && ! empty( $data['customer']['lname'] ) ) {
			// query by LIKE firstname AND LIKE lastname
			$query   = "SELECT pm.post_id, pm.meta_value, p.post_status FROM $wpdb->postmeta pm, $wpdb->posts p WHERE pm.meta_key = '_edd_payment_meta' AND pm.meta_value LIKE '%%" . $data['customer']['fname'] . "%%' AND pm.meta_value LIKE '%%" . $data['customer']['lname'] . "%%' AND pm.post_id = p.ID ORDER BY pm.post_id DESC";
			$results = $wpdb->get_results( $query );
		}

		if ( ! $results ) {
			// No purchase data was found
			$this->respond( 'No purchase data found.' );
		}

		// build array of purchases
		$orders = array();
		foreach( $results as $result ) {

			$order = array();
			$order['link'] = '<a target="_blank" href="' . admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $result->post_id ) . '">#' . $result->post_id . '</a>';

			$post = get_post( $result->post_id );
			$order['date'] = $post->post_date;
			unset( $post );

			$purchase = maybe_unserialize( $result->meta_value );
			$order['id']             = $result->post_id;
			$order['status']         = $result->post_status;
			$order['amount']         = edd_get_payment_amount( $result->post_id );
			$order['payment_method'] = $this->get_payment_method( $result->post_id );

			$downloads = edd_get_payment_meta_downloads( $result->post_id );
			if ( $downloads ) {

				$licensing = new EDD_Software_Licensing();
				$license_keys = '';

				foreach ( $downloads as $download ) {

					$id = isset( $purchase['cart_details'] ) ? $download['id'] : $download;
					if ( get_post_meta( $id, '_edd_sl_enabled', true ) ) {
						$license = $licensing->get_license_by_purchase( $order['id'], $id );
						$license_keys .= '<strong>' . get_the_title( $id ) . "</strong><br/>"
						                 . edd_get_price_option_name( $id, $download['options']['price_id'] ) . '<br/>'
						                 . '<a href="' . admin_url( 'edit.php?post_type=download&page=edd-licenses&action=manage_sites&license_id=' . $license->ID ). '">' . get_post_meta( $license->ID, '_edd_sl_key', true ) . '</a><br/><br/>';
					}
				}
			}

			if( isset( $license_keys ) && ! empty( $license_keys ) ) {
				$order['downloads'][] = $license_keys;
			}

			$orders[]             = $order;
		}

		$output = '';
		foreach ( $orders as $order ) {
			$output .= '<strong><i class="icon-cart"></i> ' . $order['link'] . '</strong>';

			if ( $order['status'] !== 'publish' ) {
				$output .= ' - <span style="color:orange;font-weight:bold;">' . $order['status'] . '</span>';
			}

			$output .= '<p><span class="muted">' . $order['date'] . '</span><br/>';
			$output .= edd_get_currency() . $order['amount'] . ' - ' . $order['payment_method'] . '</p>';
			$output .= '<p><i class="icon-pointer"></i><a target="_blank" href="' . admin_url( 'edit.php?post_type=download&page=edd-payment-history&edd-action=email_links&purchase_id=' . $order['id'] ) . '">' . __( 'Resend Purchase Receipt', 'edd' ) . '</a></p>';

			// buid list of items with license keys
			$output .= '<ul>';
			foreach ( $order['downloads'] as $download ) {
				$output .= '<li>' . $download . '</li>';
			}
			$output .= '</ul>';
		}

		$this->respond( $output );
	}

	/**
	 * Get the payment method used for the given $payment_id. Returns a link to the transaction in Stripe or PayPal if possible.
	 *
	 * @param int $payment_id
	 *
	 * @return string
	 */
	private function get_payment_method( $payment_id ) {
		$payment_method = edd_get_payment_gateway( $payment_id );

		// create link to transaction if stripe or paypal was used
		if( in_array( $payment_method, array( 'stripe', 'paypal' ) ) ) {

			$notes = edd_get_payment_notes( $payment_id );

			switch( $payment_method ) {
				case 'paypal':

					foreach ( $notes as $note ) {
						if ( preg_match( '/^PayPal Transaction ID: ([^\s]+)/', $note->comment_content, $match ) ) {
							$transaction_id = $match[1];
							$payment_method = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=' . esc_attr( $transaction_id ) . '" target="_blank">PayPal</a>';
							break;
						}
					}
					break;

				case 'stripe':
					foreach ( $notes as $note ) {
						if ( preg_match( '/^Stripe Charge ID: ([^\s]+)/', $note->comment_content, $match ) ) {
							$transaction_id = $match[1];
							$payment_method = '<a href="https:/stripe.com/payments/' . esc_attr( $transaction_id ) . '" target="_blank">Stripe</a>';
							break;
						}
					}
					break;
			}

		}

		return $payment_method;
	}

	/**
	 * Test if the provided signature is valid
	 *
	 * @return bool
	 */
	private function is_signature_valid() {

		$secret_key = defined( 'HELPSCOUT_SECRET_KEY' ) ? HELPSCOUT_SECRET_KEY : '';

		$expected_signature = base64_encode( hash_hmac( 'sha1', $this->input, $secret_key, true ) );

		if( $expected_signature !== $_SERVER['HTTP_X_HELPSCOUT_SIGNATURE'] ) {
			return false;
		}

		return true;
	}

	/**
	 * Set JSON headers, return the given response string
	 *
	 * @param $response
	 */
	private function respond( $response ) {
		$response = array( 'html' => $response );

		// clear output, some plugins might have thrown errors by now.
		if( ob_get_level() > 0 ) {
			ob_end_clean();
		}

		header( "Content-Type: application/json" );
		echo json_encode( $response );
		die();
	}

}