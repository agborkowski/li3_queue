<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace li3_queue\tests\cases\extensions\adapter\queue;

use AMQPConnection;
use AMQPConnectionException;
use li3_queue\extensions\adapter\queue\AMQP;

class AMQPTest extends \lithium\test\Unit {

	protected $_conn = null;

	protected $_testConfig = array(
		'host' => '127.0.0.1',
		'login' => 'guest',
		'password' => 'guest',
		'port' => 5672
	);

	public function skip() {
		$this->skipIf(!AMQP::enabled(), 'The `AMQP` adapter is not enabled.');

		$config = $this->_testConfig;
		$conn = new AMQPConnection($config);

		try {
			$result = $conn->connect();
		} catch (AMQPConnectionException $e) {
			$message  = "An AMQP server does not appear to be running on ";
			$message .= $config['host'] . ':' . $config['port'] . " with user `";
			$message .= $config['login'] . "` and password `" . $config['password'] . "`";
			$this->skipIf(!$e->getCode(), $message);
		}
		unset($conn);
	}

	public function testEnabled() {
		$this->assertTrue(AMQP::enabled());
	}

	public function testInit() {
		$amqp = new AMQP(array('host' => 'localhost'));
		$this->assertInternalType('object', $amqp);

		$expected = 'localhost';
		$result = $amqp->_config['host'];
		$this->assertEqual($expected, $result);

		$this->amqp = new AMQP();
	}

	public function testConnect() {
		$amqp = $this->amqp;

		$result = $amqp->connect();
		$this->assertTrue($result);
	}

	public function testSimpleWrite() {

	}

	public function testSimpleRead() {

	}

	public function testDisconnect() {
		$amqp = $this->amqp;

		$result = $amqp->disconnect();
		$this->assertTrue($result);
	}

}

?>