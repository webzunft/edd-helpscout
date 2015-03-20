<?php
/*
Plugin Name: Easy Digital Downloads integration for HelpScout
Plugin URI: https://dannyvankooten.com/helpscout-edd
Description: Easy Digital Downloads integration for HelpScout
Version: 1.0.3
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

namespace EDD_HelpScout;

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

class Plugin {

	/**
	 * @const VERSION
	 */
	const VERSION = "1.0.3";

	/**
	 * @const FILE
	 */
	const FILE = __FILE__;

	/**
	 * Constructor
	 */
	public function __construct() {

		// Load autoloader
		require __DIR__ . '/vendor/autoload.php';

		// Register activation hook
		register_activation_hook( __FILE__, array( 'EDD_HelpScout\\Admin', 'plugin_activation' ) );

		// Instantiate the plugin on a later hook
		add_action( 'plugins_loaded', array( $this, 'init' ), 90 );
	}

	/**
	 * Initialise the rest of the plugin
	 */
	public function init() {

		// do nothing if EDD is not activated
		if( ! class_exists( 'Easy_Digital_Downloads', false ) ) {
			return;
		}

		// if this is a HelpScout Request, load the Endpoint class
		if ( $this->is_helpscout_request() && ! is_admin() ) {
			new Endpoint();
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			new AJAX();
		} elseif( is_admin() ) {
			new Admin();
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

}

new Plugin();



