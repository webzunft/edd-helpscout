<?php

namespace EDD_HelpScout;

/**
 * This class takes care of AJAX requests from HelpScout
 */
class AJAX {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Double add_action because we want it to work when you're logged in too
		add_action( 'wp_ajax_nopriv_edd_helpscout_action', array( $this, 'ajax_action' ) );
		add_action( 'wp_ajax_edd_helpscout_action', array( $this, 'ajax_action' ) );
	}


	/**
	 * Handle AJAX actions
	 */
	public function ajax_action() {

		// make sure we got a signature and an action_id
		if( empty( $_GET['s'] ) || empty( $_GET['action_id'] ) ) {
			die( '-1' );
		}

		$request_signature = $_GET['s'];
		$action_id = $_GET['action_id'];

		// verify signature and referrer
		$request = new Request( $_GET );
		if( ! $request->signature_equals( $request_signature ) || ! $request->referred_from_helpscout() ) {
			die( '-1' );
		}

		switch ( $action_id ) {
			case 'deactivate-license-site':
				$this->handle_deactivation_request();
				break;
			case 'resend-purchase-receipt':
				$this->handle_purchase_receipt_resend();
				break;
			default:
				break;
		}

		die('<script>window.close();</script>');
	}

	/**
	 * Deactivates a site
	 */
	private function handle_deactivation_request() {
		$license_id = sanitize_text_field( $_REQUEST['license_id'] );
		$site_url = sanitize_text_field( $_REQUEST['site_url'] );

		edd_software_licensing()->delete_site( $license_id, $site_url );
	}

	/**
	 * Handle resending the purchase email.
	 */
	private function handle_purchase_receipt_resend() {
		$payment_id = absint( $_REQUEST['payment_id'] );

		edd_email_purchase_receipt( $payment_id, false );

		// Grab all downloads of the purchase and update their file download limits, if needed
		// This allows admins to resend purchase receipts to grant additional file downloads
		$downloads = edd_get_payment_meta_downloads( $payment_id );

		if ( is_array( $downloads ) ) {
			foreach ( $downloads as $download ) {
				$limit = edd_get_file_download_limit( $download['id'] );
				if ( ! empty( $limit ) ) {
					edd_set_file_download_limit_override( $download['id'], $payment_id );
				}
			}
		}

	}
}
