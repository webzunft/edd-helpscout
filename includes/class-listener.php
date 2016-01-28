<?php

namespace EDD\HelpScout;

class Listener {

	/**
	 * @var string
	 */
	protected $base_url = '';

	/**
	 * Listener constructor.
	 *
	 * @param string $base_url
	 */
	public function __construct( $base_url ) {
		$this->base_url = rtrim( $base_url, '/' ) . '/';
	}

	/**
	 * @param $url
	 * @return boolean
	 */
	public function listen( $url ) {

		// Make sure url starts with expected url
		if( strpos( $url, $this->base_url ) !== 0 ) {
			return '';
		}

		// extract action argument

		$url = substr( $url, strlen( $this->base_url ) );
		$url = parse_url( $url, PHP_URL_PATH );
		$pieces = explode( '/', $url );

		if( empty( $pieces[0] ) ) {
			return '';
		}

		return $pieces[0];
	}

}