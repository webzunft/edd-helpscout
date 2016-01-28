<?php

namespace EDD\HelpScout;

/**
 * This will kill the request if the current request doesn't pass authorization checks.
 */
function authorize_request() {
	if( ! verify_request_signature() || ! verify_referer() ) {
		status_header( 401 );
		exit;
	}
}

/**
 * Verify that we're coming from secure.helpscout.net.
 *
 * @return bool
 */
function verify_referer() {
	return isset( $_SERVER['HTTP_REFERER'] ) && strpos( $_SERVER['HTTP_REFERER'], 'https://secure.helpscout.net/' ) === 0;
}

/**
 * Verify the request signature that was received.
 *
 * @return bool
 */
function verify_request_signature() {

	if( empty( $_GET['s'] ) ) {
		return false;
	}

	$params = $_GET;
	$signature = $params['s'];
	unset( $params['s'] );

	$request = new Request( $params );
	return $request->signature_equals( $signature );
}

