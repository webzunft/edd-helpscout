<?php

if ( ! defined( "EDD_HS::VERSION" ) ) {
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
	 *
	 * @link http://developer.helpscout.net/custom-apps/style-guide/ HelpScout Custom Apps Style Guide
	 */
	private function process() {

		global $wpdb;

		$this->input = file_get_contents( 'php://input' );

		// check signature
		if ( ! $this->is_signature_valid() ) {
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
		$query   = "SELECT p.ID, p.post_status, p.post_date FROM {$wpdb->posts} p, {$wpdb->postmeta} pm WHERE pm.meta_key = '_edd_payment_user_email' AND pm.meta_value {$email_query} AND p.ID = pm.post_id GROUP BY p.ID  ORDER BY p.ID DESC";
		$results = $wpdb->get_results( $query );

		if ( ! is_array( $results ) || count( $results ) === 0 ) {
			// No purchase data was found
			$this->respond( 'No purchase data found.' );
		}

		// build array of purchases
		$orders = array();
		foreach ( $results as $result ) {

			$order                   = array();
			$order['id']             = $result->ID;
			$order['status']         = $result->post_status;
			$order['date']           = $result->post_date;
			$order['link']           = '<a target="_blank" href="' . admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $result->ID ) . '">#' . $result->ID . '</a>';
			$order['amount']         = edd_get_payment_amount( $result->ID );
			$order['payment_method'] = $this->get_payment_method( $result->ID );
			$order['downloads']      = array();

			$downloads = edd_get_payment_meta_downloads( $result->ID );
			if ( is_array( $downloads ) && count( $downloads ) > 0 ) {

				foreach ( $downloads as $download ) {

					$id = $download['id'];

					if ( ! $id || empty( $id ) ) {
						continue;
					}

					// generate download string
					$download_details = '<strong>' . get_the_title( $id ) . "</strong><br />";
					if ( isset( $download['options']['price_id'] ) ) {
						$download_details .= edd_get_price_option_name( $id, $download['options']['price_id'] );
					}

					if ( get_post_meta( $download['id'], '_edd_sl_enabled', true ) ) {
						$edd_sl = edd_software_licensing();

						// get license key
						$license = $edd_sl->get_license_by_purchase( $order['id'], $id );

						if ( is_object( $license ) ) {

							$license_key = get_post_meta( $license->ID, '_edd_sl_key', true );

							// add link to manage_sites for this license
							$manage_license_url = admin_url( 'edit.php?post_type=download&page=edd-licenses&s=' . $license_key );
							$download_details .= '<br /><a href="' . $manage_license_url . '">' . $license_key . '</a>';

							// get active sites for this license
							$sites = $edd_sl->get_sites( $license->ID );

							if ( is_array( $sites ) && count( $sites ) > 0 ) {

								// add active sites to the download HTML
								$download_details .= '<div class="toggleGroup">';
								$download_details .= '<a href="" class="toggleBtn"><i class="icon-arrow"></i> Active sites</a>';
								$download_details .= '<div class="toggle indent">';
								$download_details .= '<ul class="unstyled">';
								foreach ( $sites as $site ) {
									$args = array(
										'action'     => 'hs_action',
										'nonce'      => wp_create_nonce( 'hs-edd-deactivate' ),
										'hs_action'  => 'deactivate',
										'license_id' => $license->ID,
										'site_url'   => $site,
									);
									$download_details .= '<li><a href="' . esc_attr( $site ) . '" target="_blank">' . esc_html( $site ) . '</a> <a href="' . add_query_arg( $args, admin_url( 'admin-ajax.php' ) ) . '" target="_blank"><small>(deactivate)</small></a></li>';
								}
								$download_details .= '</ul>';
								$download_details .= '</div></div>';


							}

						}


					}
					$order['downloads'][] = $download_details;
				}

			}

			$orders[] = $order;
		}

		// build HTML output
		$output = '';
		foreach ( $orders as $order ) {
			$class = '';
			if ( $order['payment_method'] == 'manual_purchases' ) {
				$order['payment_method'] = 'manual';
			}
			if ( $order['status'] == 'publish' ) {
				$class = ' open';

				$args        = array(
					'action'    => 'hs_action',
					'nonce'     => wp_create_nonce( 'hs-edd-purchase-receipt' ),
					'hs_action' => 'purchase-receipt',
					'order'     => $order['id'],
				);
				$resend_link = '<a style="float:right" href="' . add_query_arg( $args, admin_url( 'admin-ajax.php' ) ) . '" target="_blank"><i title="' . __( 'Resend Purchase Receipt', 'edd' ) . '" class="icon-doc"></i></a>';

			}
			$output .= '<div class="toggleGroup' . $class . '">';

			$output .= '<strong><i class="icon-cart"></i> ' . $order['link'] . '</strong> <a class="toggleBtn"><i class="icon-arrow"></i></a>' . $resend_link;
			if ( $order['status'] !== 'publish' ) {
				$output .= '<span style="color:orange;font-weight:bold;">' . $order['status'] . '</span>';
			}

			$output .= '<div class="toggle indent">';
			$output .= '<p><span class="muted">' . $order['date'] . '</span><br/>';
			$output .= trim( edd_currency_filter( $order['amount'] ) ) . ( ( isset( $order['payment_method'] ) && '' != $order['payment_method'] ) ?  ' - ' . $order['payment_method'] : '' ) . '</p>';

			if ( ! empty( $order['downloads'] ) && count( $order['downloads'] ) > 0 ) {
				// buid list of items with license keys
				$output .= '<ul class="unstyled">';
				foreach ( $order['downloads'] as $download ) {
					$output .= '<li>' . $download . '</li>';
				}
				$output .= '</ul>';
			}
			$output .= '</div></div>';
			$output .= '<div class="divider"></div>';
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
		if ( in_array( $payment_method, array( 'stripe', 'paypal' ) ) ) {

			$notes = edd_get_payment_notes( $payment_id );

			switch ( $payment_method ) {
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
							$payment_method = '<a href="https://dashboard.stripe.com/payments/' . esc_attr( $transaction_id ) . '" target="_blank">Stripe</a>';
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

		if ( $expected_signature !== $_SERVER['HTTP_X_HELPSCOUT_SIGNATURE'] ) {
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
		if ( ob_get_level() > 0 ) {
			ob_end_clean();
		}

		header( "Content-Type: application/json" );
		echo json_encode( $response );
		die();
	}

}
