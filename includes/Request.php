<?php

namespace EDD_HelpScout;

/**
 *
 */
class Request {

	/**
	 * @var string
	 */
	public $signature = '';

	/**
	 * @var array
	 */
	private $data;

	/**
	 * @var string
	 */
	private $secret_key = '';

	/**
	 * @param array $data
	 */
	public function __construct( $data ) {
		$this->secret_key = defined( 'HELPSCOUT_SECRET_KEY' ) ? HELPSCOUT_SECRET_KEY : '';
		$this->data = $data;
		$this->signature = $this->create_expected_signature();
	}

	/**
	 * @return string
	 */
	private function create_expected_signature() {
		return base64_encode( hash_hmac( 'sha1', json_encode( $this->data ), $this->secret_key, true ) );
	}

	/**
	 * @param $signature
	 *
	 * @return bool
	 */
	public function signature_equals( $signature ) {

		// use `hash_equals( str1, str2 )` if it exists
		if( function_exists( 'hash_equals' ) ) {
			return hash_equals( $this->signature, $signature );
		}

		return $this->signature === $signature;
	}

	/**
	 * @return bool
	 */
	public function referred_from_helpscout() {
		return ( isset( $_SERVER['HTTP_REFERER'] ) && strpos( $_SERVER['HTTP_REFERER'], 'https://secure.helpscout.net/' ) === 0 );
	}

	/**
	 * @return string
	 */
	public function get_signed_admin_url() {

		$args = $this->data;

		// add signature to url args
		$args['s'] = $this->signature;

		return add_query_arg( urlencode_deep( $args ), admin_url( 'admin-ajax.php' ) );
	}


}