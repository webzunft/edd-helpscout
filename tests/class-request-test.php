<?php

namespace EDD\HelpScout;

use PHPUnit_Framework_TestCase;

/**
 * Class RequestTest
 *
 * @ignore
 */
class RequestTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers Request::__construct
	 */
	public function test_constructor() {
		$data = [ 'key' => 'value' ];
		$request = new Request( $data );
		self::assertNotEmpty( $request->signature );
	}

	/**
	 * @covers Request::signature_equals
	 */
	public function test_signature_equals() {
		$data = [ 'key' => 'value' ];
		$request = new Request( $data );

		$signature = base64_encode( hash_hmac( 'sha1', json_encode( $data ), HELPSCOUT_SECRET_KEY, true ) );
		self::assertTrue( $request->signature_equals( $signature ) );
		self::assertFalse( $request->signature_equals( '' ) );

		$signature = base64_encode( hash_hmac( 'sha1', json_encode( [ 'other-key' => 'value' ] ), HELPSCOUT_SECRET_KEY, true ) );
		self::assertFalse( $request->signature_equals( $signature ) );
	}
}