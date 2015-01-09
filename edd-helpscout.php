<?php
/*
Plugin Name: Easy Digital Downloads integration for HelpScout
Plugin URI: https://dannyvankooten.com/helpscout-edd
Description: Easy Digital Downloads integration for HelpScout
Version: 1.0.2
Author: Danny van Kooten
Author URI: https://dannyvankooten.com
Text Domain: edd-helpscout
Domain Path: /languages
License: GPL v3

Easy Digital Downloads integration for HelpScout
Copyright (C) 2012-2013, Danny van Kooten, hi@dannyvankooten.com

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

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

class EDD_HS {

	/**
	 * @const VERSION
	 */
	const VERSION = "1.0.2";

	/**
	 * @const FILE
	 */
	const FILE = __FILE__;


	/**
	 * Constructor
	 */
	public function __construct() {

		// do nothing if EDD is not activated
		if( ! class_exists( 'Easy_Digital_Downloads', false ) ) {
			return;
		}

		// register autoloader
		spl_autoload_register( array( $this, 'autoload' ) );

		// if this is a HelpScout Request, load the Endpoint class
		if ( $this->is_helpscout_request() && ! is_admin() ) {
			new EDD_HS_Endpoint();
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			new EDD_HS_Ajax();
		} elseif( is_admin() ) {
			new EDD_HS_Admin();
		}
	}

	/**
	 * Is this a request we should respond to?
	 *
	 * @return bool
	 */
	private function is_helpscout_request() {

		$trigger = stristr( $_SERVER['REQUEST_URI'], '/edd-hs-api/customer-data.json' ) !== false;

		if( ! $trigger && isset( $_SERVER['HTTP_X_HELPSCOUT_SIGNATURE'] ) ) {

			$greedy = get_option( 'edd_hs_greedy_listening', 1 );

			if( $greedy ) {
				$trigger = true;
			}
		}

		return (bool) apply_filters( 'edd_hs/is_helpscout_request', $trigger );
	}

	/**
	 * @param string $class
	 *
	 * @return bool
	 */
	public function autoload( $class ) {

		// only act on classnames with this prefix
		if( strpos( $class, 'EDD_HS_' ) !== 0 ) {
			return false;
		}

		$filename = dirname( __FILE__ ) . '/includes/class-' . strtolower( substr( $class, 7 ) ) . '.php';

		if( file_exists( $filename ) ) {
			require_once( $filename );
			return true;
		}

		return false;
	}

}

/**
 * Initiate the EDD_HS class
 */
function __load_edd_helpscout() {
	new EDD_HS;
}

/**
 * Disable greedy listening for new plugin users
 */
function edd_hs_disable_greedy_listening() {
	update_option( 'edd_hs_greedy_listening', 0 );
}


// Instantiate the plugin on a later hook
add_action( 'plugins_loaded', '__load_edd_helpscout', 90 );

// Register activation hook
register_activation_hook( __FILE__, 'edd_hs_disable_greedy_listening' );

