<?php

namespace EDD\HelpScout;

use PHPUnit_Framework_TestCase;

/**
 * Class ListenerTest
 *
 * @ignore
 */
class ListenerTest extends PHPUnit_Framework_TestCase {

	/**
	 * @covers Listener::listen
	 */
	public function test_listen() {
		$instance = new Listener( '/base' );

		self::assertEmpty( $instance->listen( '/not-base' ) );
		self::assertEmpty( $instance->listen( '/base' ) );
		self::assertEmpty( $instance->listen( '/base/' ) );
		self::assertEquals( $instance->listen('/base/action'), 'action' );
		self::assertEquals( $instance->listen('/base/action/'), 'action');
		self::assertEquals( $instance->listen('/base/action/?id=50'), 'action');
	}
}