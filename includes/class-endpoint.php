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

	const SECRET_KEY = 'ABRowClEq8OCBFxhw1eBu7AAakk2HswKdeIC4JWf';

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

		if ( isset( $data['customer']['emails'] ) && is_array( $data['customer']['emails'] ) ) {
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
		$query   = "SELECT pm2.post_id, pm2.meta_value, p.post_status FROM $wpdb->postmeta pm, $wpdb->postmeta pm2, $wpdb->posts p WHERE pm.meta_key = '_edd_payment_user_email' AND pm.meta_value $email_query AND pm.post_id = pm2.post_id AND pm2.meta_key = '_edd_payment_meta' AND pm.post_id = p.ID AND p.post_status NOT IN ('failed','revoked') ORDER BY pm.post_id DESC";
		$results = $wpdb->get_results( $query );

		if ( ! $results ) {
			// query by LIKE firstname AND LIKE lastname
			$query   = "SELECT pm.post_id, pm.meta_value, p.post_status FROM $wpdb->postmeta pm, $wpdb->posts p WHERE pm.meta_key = '_edd_payment_meta' AND pm.meta_value LIKE '%%" . $data['customer']['fname'] . "%%' AND pm.meta_value LIKE '%%" . $data['customer']['lname'] . "%%' AND pm.post_id = p.ID AND p.post_status NOT IN ('failed','revoked') ORDER BY pm.post_id DESC";
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
			$order['payment_method'] = edd_get_payment_gateway( $result->post_id );

			if ( 'paypal' == $order['payment_method'] ) {
				// Grab the PayPal transaction ID and link the transaction to PayPal
				$notes = edd_get_payment_notes( $result->post_id );
				foreach ( $notes as $note ) {
					if ( preg_match( '/^PayPal Transaction ID: ([^\s]+)/', $note->comment_content, $match ) )
						$order['paypal_transaction_id'] = $match[1];
				}

				$order['payment_method'] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=' . $order['paypal_transaction_id'] . '" target="_blank">PayPal</a>';
			}

			$downloads = maybe_unserialize( $purchase['downloads'] );
			if ( $downloads ) {
				$license_keys = '';
				foreach ( maybe_unserialize( $purchase['downloads'] ) as $download ) {

					$id = isset( $purchase['cart_details'] ) ? $download['id'] : $download;

					$licensing = new EDD_Software_Licensing();

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
			if ( $order['status'] != 'publish' )
				$output .= ' - <span style="color:orange;font-weight:bold;">' . $order['status'] . '</span>';
			$output .= '<p><span class="muted">' . $order['date'] . '</span><br/>';
			$output .= '$' . $order['amount'] . ' - ' . $order['payment_method'] . '</p>';
			$output .= '<p><i class="icon-pointer"></i><a target="_blank" href="' . admin_url( 'edit.php?post_type=download&page=edd-payment-history&edd-action=email_links&purchase_id=' . $order['id'] ) . '">' . __( 'Resend Purchase Receipt', 'edd' ) . '</a></p>';
			$output .= '<ul>';
			foreach ( $order['downloads'] as $download ) {
				$output .= '<li>' . $download . '</li>';
			}
			$output .= '</ul>';
		}

		$this->respond( $output );
	}

	/**
	 * Test if the provided signature is valid
	 *
	 * @return bool
	 */
	private function is_signature_valid() {
		$expected_signature = base64_encode( hash_hmac( 'sha1', $this->input, self::SECRET_KEY, true ) );

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