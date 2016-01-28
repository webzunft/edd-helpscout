<?php

namespace EDD\HelpScout;

defined( 'ABSPATH' ) or exit;

// Load autoloader
require __DIR__ . '/vendor/autoload.php';

// Load default API actions
require __DIR__ . '/includes/default-actions.php';

// Check for API actions
if( ! is_admin() ) {

	$listener = new Listener( '/edd-helpscout-api' );
	$action = $listener->listen( $_SERVER['REQUEST_URI'] );

	if( ! empty( $action ) ) {

		/**
		 * Perform an API action. Request is unauthorized at this point, so make sure to perform auth checks in your action callback.
		 *
		 * The dynamic portion of the hook, `$action`, refers to the name of the action.
		 *
		 * @see authorize_request
		 * @see verify_request_signature
		 */
		do_action( 'edd_helpscout_' . $action );
	}
}