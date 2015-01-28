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
	 * @var array|mixed
	 */
	private $data;

	/**
	 * @var array
	 */
	private $customer_emails = array();

	/**
	 * @var array
	 */
	private $customer_payments = array();

	/**
	 * Constructor
	 */
	public function __construct() {

		// get request data
		$this->data = $this->parse_data();

		// validate request
		if( ! $this->validate() ) {
			$this->respond( 'Invalid signature' );
			exit;
		}

		// get customer email(s)
		$this->customer_emails = $this->get_customer_emails();

		// get customer payment(s)
		$this->customer_payments = $this->query_customer_payments();

		// build the final response HTML for HelpScout
		$html = $this->build_response_html();

		// respond with the built HTML string
		$this->respond( $html );
	}

	/**
	 * @return array|mixed
	 */
	private function parse_data() {

		$data_string = file_get_contents( 'php://input' );
		$data = json_decode( $data_string, true );

		return $data;
	}

	/**
	 * Validate the request
	 *
	 * - Validates the payload
	 * - Validates the request signature
	 *
	 * @return bool
	 */
	private function validate() {

		// we need at least this
		if ( ! isset( $this->data['customer']['email'] ) && ! isset( $this->data['customer']['emails'] ) ) {
			return false;
		}

		// check request signature
		$request = new EDD_HS_Request( $this->data );

		if ( isset( $_SERVER['HTTP_X_HELPSCOUT_SIGNATURE'] ) && $request->signature_equals( $_SERVER['HTTP_X_HELPSCOUT_SIGNATURE'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get an array of emails belonging to the customer
	 *
	 * @return array
	 */
	private function get_customer_emails() {

		$customer_data = $this->data['customer'];
		$emails = array();

		if( isset( $customer_data['emails'] ) && is_array( $customer_data['emails'] ) && count( $customer_data['emails'] ) > 1 ) {
			$emails = array_values( $customer_data['emails'] );
		} elseif( isset( $customer_data['email'] ) ) {
			$emails = array( $customer_data['email'] );
		}

		$emails = apply_filters( 'helpscout_edd_customer_emails', $emails, $this->data );

		if( count( $emails ) === 0 ) {
			$this->respond( 'No customer email given.' );
		}

		return $emails;
	}

	/**
	 * Query all payments belonging to the customer (by email)
	 *
	 * @return array
	 */
	private function query_customer_payments() {

		global $wpdb;

		$emails = rtrim( implode( "','", $this->customer_emails ), ",'" );
		// query by email(s)
		$sql   = "SELECT p.ID, p.post_status, p.post_date FROM {$wpdb->posts} p, {$wpdb->postmeta} pm WHERE pm.meta_key = '_edd_payment_user_email'";
		$sql .= " AND pm.meta_value IN('$emails') AND p.ID = pm.post_id GROUP BY p.ID  ORDER BY p.ID DESC";

		$results = $wpdb->get_results( $sql );

		if( is_array( $results ) ) {
			return $results;
		}

		return array();
	}

	/**
	 * Process the request
	 *  - Find purchase data
	 *  - Generate response
	 *
	 * @TODO: Refactor out loop to find additional order data.
	 *
	 * @link http://developer.helpscout.net/custom-apps/style-guide/ HelpScout Custom Apps Style Guide
	 * @return string
	 */
	private function build_response_html() {

		if ( count( $this->customer_payments ) === 0 ) {
			// No purchase data was found
			return 'No purchase data found';
		}

		// build array of purchases
		$orders = array();
		foreach ( $this->customer_payments as $payment ) {

			$order                   = array();
			$order['id']             = $payment->ID;
			$order['status']         = $payment->post_status;
			$order['date']           = $payment->post_date;
			$order['link']           = '<a target="_blank" href="' . admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $payment->ID ) . '">#' . $payment->ID . '</a>';
			$order['amount']         = edd_get_payment_amount( $payment->ID );
			$order['payment_method'] = $this->get_payment_method( $payment->ID );
			$order['downloads']      = array();

			$downloads = edd_get_payment_meta_downloads( $payment->ID );
			if ( is_array( $downloads ) && count( $downloads ) > 0 ) {

				foreach ( $downloads as $download ) {

					$id = $download['id'];

					if ( ! $id || empty( $id ) ) {
						continue;
					}

					// generate download string
					$download_details = '<strong>' . get_the_title( $id ) . "</strong><br />";
					$download_details .= edd_get_price_option_name( $id, $download['options']['price_id'] );

					// query license keys if order is completed and has licensing enabled
					if ( $order['status'] === 'publish' &&  get_post_meta( $download['id'], '_edd_sl_enabled', true ) && function_exists( 'edd_software_licensing' ) ) {
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
										'hs_action'  => 'deactivate',
										'license_id' => (string) $license->ID,
										'site_url'   => $site,
									);
									$request = new EDD_HS_Request( $args );
									if ( strpos( $site, 'http' ) !== 0 ) {
										$site = 'http://' . $site;
									}
									$download_details .= '<li><a href="' . esc_url( $site ) . '" target="_blank">' . esc_html( $site ) . '</a> <a href="' . esc_url( 
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
		$html = '';
		foreach ( $orders as $order ) {

			$class = '';

			// open completed purchases by default
			if ( $order['status'] === 'publish' ) {
				$class = ' open';
			}

			$html .= '<div class="toggleGroup' . $class . '">';
			$html .= '<strong><i class="icon-cart"></i> ' . $order['link'] . '</strong> <a class="toggleBtn"><i class="icon-arrow"></i></a>';

			// show status if order wasn't completed. otherwise, show resend receipt icon.
			if ( $order['status'] !== 'publish' ) {
				$html .= '<span style="color:orange;font-weight:bold;">' . $order['status'] . '</span>';
			} else {

				// was this a renewaL?
				if( '' !== (string) get_post_meta( $order['id'], '_edd_sl_is_renewal', true ) ) {
					$html .= '<span style="color:#008000;font-weight:bold;">renewal</span>';
				}

				// add icon to resend purchase receipt
				$args = array(
					'action'    => 'hs_action',
					'hs_action' => 'purchase-receipt',
					'order'     => (string) $order['id'],
				);
				$request = new EDD_HS_Request( $args );
				$resend_link = '<a style="float:right" href="' . esc_url( $request->get_signed_admin_url() ) . '" target="_blank"><i title="' . __( 'Resend Purchase Receipt', 'edd' ) . '" class="icon-doc"></i></a>';
				$html .=  $resend_link;
			}

			$html .= '<div class="toggle indent">';
			$html .= '<p><span class="muted">' . $order['date'] . '</span><br/>';
			$html .= trim( edd_currency_filter( $order['amount'] ) ) . ( ( isset( $order['payment_method'] ) && '' !== $order['payment_method'] ) ?  ' - ' . $order['payment_method'] : '' ) . '</p>';

			if ( ! empty( $order['downloads'] ) && count( $order['downloads'] ) > 0 ) {
				// buid list of items with license keys
				$html .= '<ul class="unstyled">';
				foreach ( $order['downloads'] as $download ) {
					$html .= '<li>' . $download . '</li>';
				}
				$html .= '</ul>';
			}
			$html .= '</div></div>';
			$html .= '<div class="divider"></div>';
		}

		return $html;
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

		switch ( $payment_method ) {
			case 'paypal':
				$notes = edd_get_payment_notes( $payment_id );
				foreach ( $notes as $note ) {
					if ( preg_match( '/^PayPal Transaction ID: ([^\s]+)/', $note->comment_content, $match ) ) {
						$transaction_id = $match[1];
						$payment_method = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=' . esc_attr( $transaction_id ) . '" target="_blank">PayPal</a>';
						break;
					}
				}
				break;

			case 'stripe':
				$notes = edd_get_payment_notes( $payment_id );
				foreach ( $notes as $note ) {
					if ( preg_match( '/^Stripe Charge ID: ([^\s]+)/', $note->comment_content, $match ) ) {
						$transaction_id = $match[1];
						$payment_method = '<a href="https://dashboard.stripe.com/payments/' . esc_attr( $transaction_id ) . '" target="_blank">Stripe</a>';
						break;
					}
				}
				break;
			case 'manual_purchases':
				$payment_method = 'Manual';
				break;
		}

		return $payment_method;
	}

	/**
	 * Set JSON headers, return the given response string
	 *
	 * @param string $html
	 */
	private function respond( $html ) {
		$response = array( 'html' => $html );

		// clear output, some plugins might have thrown errors by now.
		if ( ob_get_level() > 0 ) {
			ob_end_clean();
		}

		header( "Content-Type: application/json" );
		echo json_encode( $response );
		die();
	}

}
