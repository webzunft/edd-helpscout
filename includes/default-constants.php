<?php

defined( 'ABSPATH' ) or exit;

// define API path (if it's not set already)
if( ! defined( 'EDD_HELPSCOUT_API_PATH' ) ) {
	define( 'EDD_HELPSCOUT_API_PATH', '/edd-helpscout-api/' );
}

if( ! defined( 'HELPSCOUT_SECRET_KEY' ) ) {
	define( 'HELPSCOUT_SECRET_KEY', wp_generate_password( 40 ) );
	trigger_error( sprintf( "Please set the %s constant in your %s file.", 'HELPSCOUT_SECRET_KEY', 'wp-config.php' ) );
}