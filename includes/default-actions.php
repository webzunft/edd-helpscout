<?php

namespace EDD\HelpScout;

defined( 'ABSPATH' ) or exit;

/**
 * Resends a purchase receipt
 */
function resend_purchase_receipt() {
	authorize_request();

	$payment_id = absint( $_GET['payment_id'] );

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
	die('<script>window.close();</script>');
}

/**
 * Deactivates the given site
 */
function deactivate_site_license() {
	authorize_request();

	$license_id = sanitize_text_field( $_GET['license_id'] );
	$site_url = sanitize_text_field( $_GET['site_url'] );

	edd_software_licensing()->delete_site( $license_id, $site_url );
	die('<script>window.close();</script>');
}

/**
 * Get customer info
 */
function get_customer_info() {
	new Endpoint();
}


add_action( 'edd_helpscout_resend_purchase_receipt', 'EDD\\HelpScout\\resend_purchase_receipt' );
add_action( 'edd_helpscout_deactivate_site_license', 'EDD\\HelpScout\\deactivate_site_license' );
add_action( 'edd_helpscout_customer_info', 'EDD\\HelpScout\\get_customer_info' );

