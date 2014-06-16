<?php
/*
Plugin Name: Easy Digital Downloads integration for HelpScout
Plugin URI: http://dannyvankooten.com/helpscout-edd
Description: Easy Digital Downloads integration for HelpScout
Version: 1.0
Author: Danny van Kooten
Author URI: http://dannyvankooten.com
Text Domain: helpscout-edd
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
if( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

class EDD_HS {

	const VERSION = "1.0";
	const FILE = __FILE__;

	public function __construct() {

		// register autoloader
		spl_autoload_register( array( $this, 'autoload') );

		// load plugin files on later hook
		add_action( 'plugins_loaded', array( $this, 'load' ), 90 );
	}

	public function load() {

		// if this is a HelpScout Request, load the Endpoint class
		if( isset( $_SERVER['HTTP_X_HELPSCOUT_SIGNATURE'] ) ) {
			new EDD_HS_Endpoint();
		}

	}

	public function autoload( $class ) {
		static $classes = null;

		if( $classes === null ) {

			$include_path = dirname( __FILE__ ) . '/includes/';

			$classes = array(
				'edd_hs_endpoint'   => $include_path . 'class-endpoint.php',
			);
		}

		$class_name = strtolower( $class );

		if( isset( $classes[$class_name] ) ) {
			require_once $classes[$class_name];
			return true;
		}

		return false;
	}
}

new EDD_HS;
