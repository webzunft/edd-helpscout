<?php
/*
Plugin Name: Easy Digital Downloads integration for HelpScout
Plugin URI: https://dannyvankooten.com/
Description: Easy Digital Downloads integration for HelpScout
Version: 2.0-beta
Author: Danny van Kooten
Author URI: https://dannyvankooten.com
Text Domain: edd-helpscout
Domain Path: /languages
License: GPL v3

Easy Digital Downloads integration for HelpScout
Copyright (C) 2013-2016, Danny van Kooten, hi@dannyvankooten.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


// prevent dire file access
defined( 'ABSPATH' ) or exit;

// define some useful constants
define( 'EDD_HELPSCOUT_VERSION', '2.0-beta' );
define( 'EDD_HELPSCOUT_FILE', __FILE__ );


/**
 * @ignore
 */
function _load_edd_helpscout() {

	// do nothing if EDD is not activated
	if( ! class_exists( 'Easy_Digital_Downloads' ) ) {
		return;
	}

	// do nothing if PHP is below version 5.3
	if( version_compare( PHP_VERSION, '5.3', '<' ) ) {
		return;
	}

	// go!
	require __DIR__ . '/bootstrap.php';
}

/**
 * Bootstrap the plugin at `plugins_loaded` (after EDD)
 */
add_action( 'plugins_loaded', '_load_edd_helpscout' , 90 );
