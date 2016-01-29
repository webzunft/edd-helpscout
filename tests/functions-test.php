<?php

namespace EDD\HelpScout;

use PHPUnit_Framework_TestCase;

/**
 * Class ListenerTest
 *
 * @ignore
 */
class FunctionsTest extends PHPUnit_Framework_TestCase {


	/**
	 * @covers verify_refer
	 */
	public function test_verify_referer() {
		$_SERVER['HTTP_REFERER'] = 'https://google.com/';
		self::assertFalse(verify_referer());

		$_SERVER['HTTP_REFERER'] = 'https://secure.helpscout.net/';
		self::assertTrue(verify_referer());

		$_SERVER['HTTP_REFERER'] = 'https://secure.helpscout.net/path/';
		self::assertTrue(verify_referer());
	}

	/**
	 * @covers verify_signature
	 * @incomplete
	 */
	public function test_verify_signature() {

		// GET param 's' not set
		self::assertFalse(verify_request_signature());
	}

}