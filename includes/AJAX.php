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
		add_action( 'wp_ajax_nopriv_hs_action', array( $this, 'ajax_action' ) );
		add_action( 'wp_ajax_hs_action', array( $this, 'ajax_action' ) );
	}


	/**
	 * Handle AJAX actions
	 */
	public function ajax_action() {

		// Verify request
		if( isset( $_GET['s'] ) ) {
			$given_signature = isset( $_GET['s'] ) ? $_GET['s'] : '';
			unset( $_GET['s'] );
		} else {
			$given_signature = '';
		}

		$request = new Request( $_GET );

		// verify signature and referrer
		if( ! $request->signature_equals( $given_signature )  || ! $request->referred_from_helpscout() ) {
			die( '-1' );
		}

		switch ( $_REQUEST['hs_action'] ) {
			case 'deactivate':
				$this->handle_deactivation_request();
				break;
			case 'purchase-receipt':
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
		$purchase_id = absint( $_REQUEST['order'] );

		edd_email_purchase_receipt( $purchase_id, false );

		// Grab all downloads of the purchase and update their file download limits, if needed
		// This allows admins to resend purchase receipts to grant additional file downloads
		$downloads = edd_get_payment_meta_downloads( $purchase_id );

		if ( is_array( $downloads ) ) {
			foreach ( $downloads as $download ) {
				$limit = edd_get_file_download_limit( $download['id'] );
				if ( ! empty( $limit ) ) {
					edd_set_file_download_limit_override( $download['id'], $purchase_id );
				}
			}
		}

	}
}
