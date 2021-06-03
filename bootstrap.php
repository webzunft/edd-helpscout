<?php

namespace EDD\HelpScout;

defined( 'ABSPATH' ) or exit;

// Load autoloader (but only if not loaded already, to work with site-wide autoloaders)
if( ! function_exists( 'EDD\\HelpScout\\authorize_request' ) ) {
	// Load helper functions
	require_once __DIR__ . '/includes/functions.php';

	// autoload classes
	spl_autoload_register( function( $classname ) {
		if ( strpos( $classname, 'EDD\\HelpScout\\') !== false ) {
			$class      = basename( str_replace( '\\', DIRECTORY_SEPARATOR, $classname ) );
			$classpath  = __DIR__ .  DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'class-'. strtolower( $class ) . '.php';
			if ( file_exists( $classpath ) ) {
				include_once $classpath;
			}
		}
	} );
}

// define some default constants
require_once __DIR__ . '/includes/default-constants.php';

// Load default API actions
require_once __DIR__ . '/includes/default-actions.php';

// Check for API actions
if( ! is_admin() ) {
	add_action( 'init', 'EDD\\HelpScout\\listen_for_actions' );
}