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

        // Multisite fix: In case the REQUEST_URI starts with the blog path in the URL (e.g. /de/edd-helpscout-api/customer_info) the URL would be declined later
        if ( is_multisite() && isset ( $GLOBALS['blog_id'] ) ) {
            $blog_details =  get_blog_details( $GLOBALS['blog_id'] );

            // Removing the blog path from the beginning of the URL
            if ( ! empty ( $blog_details->path ) && substr( $url, 0, strlen( $blog_details->path ) ) === $blog_details->path) {
                $url = '/' . substr( $url, strlen( $blog_details->path ) );
            }
        }

        // warn if user still uses /edd-helpscout/api as prior v 2.0 is still used here
        if( strpos( $url, '/edd-helpscout/api' ) === 0 ) {
			trigger_error( sprintf( __( '`%1$s` is deprecated since version %2$s! Follow the installation instructions and use `%3$s` in your Help Scount account instead.', 'edd-helpscout' ), '/edd-helpscout/api', '2.0', '/edd-helpscout-api/customer_info' ) );
		}

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