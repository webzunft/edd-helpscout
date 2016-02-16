<?php

namespace EDD\HelpScout;

use EDD_Software_Licensing;

/**
 * This class takes care of requests coming from HelpScout App Integrations
 */
class Endpoint {

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
         * @var string 
         */
        private $customer_license_key;

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

                // get customer license key
                $this->customer_license_key = $this->get_customer_license_key();

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
		$request = new Request( $this->data );

		if ( isset( $_SERVER['HTTP_X_HELPSCOUT_SIGNATURE'] ) && $request->signature_equals( $_SERVER['HTTP_X_HELPSCOUT_SIGNATURE'] ) ) {
			return true;
		}

		return false;
	}

	/**
         * Get the license key of the customer from subject line of the ticket
         * Key must be last element of the subject line separated by at least one blank space
         * 
         * @return string
         */
        private function get_customer_license_key() {
            $ticket_data = $this->data['ticket'];
            $subject = $ticket_data['subject'];
            $trim_spaces = preg_replace('/\s+/', ' ',$subject);
            
            $parts = explode(' ', $trim_spaces);
            $key = array_pop($parts);

            return $key;
        }
        
        /**
         * get customer mail address by license key
         * 
         * @return mixed string|false
         */
        private function get_customer_mail_by_key(){
            if (!class_exists('EDD_Software_Licensing'))
                return false;
            
                $license     = $this->customer_license_key;
                $license_id  = EDD_Software_Licensing::get_license_by_key( $license );
		$payment_id  = get_post_meta( $license_id, '_edd_sl_payment_id', true );
		$user_info   = edd_get_payment_meta_user_info( $payment_id );
                
                if (isset($user_info['email']))
                    return $user_info['email'];
                
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

                // try to get email address by license key 
                if ( is_string($this->get_customer_mail_by_key() ) )  
                    $emails[] = $this->get_customer_mail_by_key();
                

		if( isset( $customer_data['emails'] ) && is_array( $customer_data['emails'] ) && count( $customer_data['emails'] ) > 1 ) {
			$emails[] = array_values( $customer_data['emails'] );
		} elseif( isset( $customer_data['email'] ) ) {
			$emails[] = $customer_data['email'];
		}

		/**
		 * Filter email address of the customer
		 * @since 1.1
		 */
		$emails = apply_filters( 'edd_helpscout_customer_emails', $emails, $this->data );

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

		$payments = array();

		/**
		 * Allows you to perform your own search for customer payments, based on given data.
		 *
		 * @since 1.1
		 */
		$payments = apply_filters( 'edd_helpscout_customer_payments', $payments, $this->customer_emails, $this->data );

		if( ! empty( $payments ) ) {
			return $payments;
		}

		global $wpdb;

		// query by email(s)
		$sql  = "SELECT p.ID, p.post_status, p.post_date";
		$sql .= " FROM {$wpdb->posts} p, {$wpdb->postmeta} pm";
		$sql .= " WHERE p.post_type = 'edd_payment'";
		$sql .= " AND p.ID = pm.post_id";
		$sql .= " AND pm.meta_key = '_edd_payment_user_email'";

		if( count( $this->customer_emails ) > 1 ) {
			$in_clause = rtrim( str_repeat( "'%s', ", count( $this->customer_emails ) ), ", " );
			$sql .= " AND pm.meta_value IN($in_clause)";
		} else {
			$sql .= " AND pm.meta_value = '%s'";
		}

		$sql .= " GROUP BY p.ID  ORDER BY p.ID DESC";

		$query = $wpdb->prepare( $sql, $this->customer_emails );
		$results = $wpdb->get_results( $query );

		if( is_array( $results ) ) {
			return $results;
		}

		return array();
	}

	/**
	 * Process the request
	 *  - Find purchase data
	 *  - Generate response*
	 * @link http://developer.helpscout.net/custom-apps/style-guide/ HelpScout Custom Apps Style Guide
	 * @return string
	 */
	private function build_response_html() {

		if ( count( $this->customer_payments ) === 0 ) {


			// No purchase data was found
			return sprintf( '<p>No payments founds for %s.</p>', '<strong>' . join( '</strong> or <strong>', $this->customer_emails ) . '</strong>' );
		}

		// build array of purchases
		$orders = array();
		foreach ( $this->customer_payments as $payment ) {

			$order                   = array();
			$order['payment_id']     = $payment->ID;
			$order['date']           = $payment->post_date;
			$order['amount']         = edd_get_payment_amount( $payment->ID );
			$order['status']         = $payment->post_status;
			$order['payment_method'] = $this->get_payment_method( $payment->ID );
			$order['downloads']      = array();
			$order['resend_receipt_link'] = '';
			$order['is_renewal'] = false;
			$order['is_completed']   = ( $payment->post_status === 'publish' );

			// do stuff for completed orders
			if( $payment->post_status === 'publish' ) {
				$args = array(
					'payment_id'    => (string) $order['payment_id'],
				);
				$request = new Request( $args );
				$order['resend_receipt_link'] = $request->get_signed_url( 'resend_purchase_receipt' );
			}

			// find purchased Downloads.
			$order['downloads'] = (array) edd_get_payment_meta_downloads( $payment->ID );

			// for each download, find license + sites
			if( function_exists( 'edd_software_licensing' ) ) {

				/**
				 * @var EDD_Software_Licensing
				 */
				$licensing = edd_software_licensing();

				// was this order a renewal?
				$order['is_renewal'] = ( (string) get_post_meta( $payment->ID, '_edd_sl_is_renewal', true ) !== '' );

				if( $order['is_completed'] ) {
					foreach( $order['downloads'] as $key => $download ) {

						// only proceed if this download has EDD Software Licensing enabled
						if( '' === (string) get_post_meta( $download['id'], '_edd_sl_enabled', true ) ) {
							continue;
						}

						// find license that was given out for this download purchase
						$license = $licensing->get_license_by_purchase( $payment->ID, $download['id'] );

						if( is_object( $license ) ) {
							$key =  (string) get_post_meta( $license->ID, '_edd_sl_key', true );

							// add support for "lifetime" licenses
							if ( method_exists( $licensing, 'is_lifetime_license' ) && $licensing->is_lifetime_license( $license->ID ) ) {
								$is_expired = false;
							} else {
								$expires = (string) get_post_meta( $license->ID, '_edd_sl_expiration', true );
								$is_expired = $expires < time();
							}

							$order['downloads'][$key]['license'] = array(
								'limit' => 0,
								'key' => $key,
								'is_expired' => $is_expired,
								'sites' => array()
							);

							// look-up active sites if license is not expired
							if( ! $is_expired ) {

								// get license limit
								$order['downloads'][$key]['license']['limit'] = $licensing->get_license_limit( $download['id'], $license->ID );
								$sites = (array) $licensing->get_sites( $license->ID );

								foreach( $sites as $site ) {
									$args = array(
										'license_id' => (string) $license->ID,
										'site_url'   => $site,
									);

									// make sure site url is prefixed with "http://"
									$site_url = strpos( $site, '://' ) !== false ? $site : 'http://' . $site;

									$request   = new Request( $args );
									$order['downloads'][$key]['license']['sites'][] = array(
										'url' => $site_url,
										'deactivate_link' => $request->get_signed_url( 'deactivate_site_license' )
									);


								}
							} //endif not expired
						} // endif license found
					} // end foreach downloads
				} // endif order completed
			}

			$orders[] = $order;
		}

		// build HTML output
		$html = '';
		foreach ( $orders as $order ) {
			$html .= str_replace( '\t', '', $this->order_row( $order ) );
		}

		return $html;
	}

	/**
	 * @param $order
	 *
	 * @return string
	 */
	public function order_row( array $order ) {
		ob_start();
		include dirname( EDD_HELPSCOUT_FILE ) . '/views/order-row.php';
		$html = ob_get_clean();
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
						break 2;
					}
				}
				break;

			case 'stripe':
				$notes = edd_get_payment_notes( $payment_id );
				foreach ( $notes as $note ) {
					if ( preg_match( '/^Stripe Charge ID: ([^\s]+)/', $note->comment_content, $match ) ) {
						$transaction_id = $match[1];
						$payment_method = '<a href="https://dashboard.stripe.com/payments/' . esc_attr( $transaction_id ) . '" target="_blank">Stripe</a>';
						break 2;
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
	 * @param string $html HTML content of the response
	 * @param int $code The HTTP status code to respond with
	 */
	private function respond( $html, $code = 200 ) {
		$response = array( 'html' => $html );

		// clear output, some plugins might have thrown errors by now.
		if ( ob_get_level() > 0 ) {
			ob_end_clean();
		}

		status_header( $code );
		header( "Content-Type: application/json" );
		echo json_encode( $response );
		die();
	}

}
