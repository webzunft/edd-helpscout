<?php

namespace EDD\HelpScout;

defined( 'ABSPATH' ) or exit;

// Load autoloader (but only if not loaded already, to work with site-wide autoloaders)
if( ! function_exists( 'EDD\\HelpScout\\authorize_request' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

// define some default constants
require_once __DIR__ . '/includes/default-constants.php';

// Load default API actions
require_once __DIR__ . '/includes/default-actions.php';

// Check for API actions
if( ! is_admin() ) {
	add_action( 'init', 'EDD\\HelpScout\\listen_for_actions' );
}