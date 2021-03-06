<?php

namespace li3_queue\tests\cases\net\beanstalk;

use li3_queue\net\beanstalk\Service;
use lithium\core\NetworkException;

class ServiceTest extends \lithium\test\Unit {

	public $service = null;

	protected $_testConfig = array(
		'host' => '127.0.0.1',
		'port' => 11300
	);

	public function skip() {
		$config = $this->_testConfig;

		$conn = new Service($config);

		try {
			$conn->connect();
		} catch (NetworkException $e) {
			$message  = "A Beanstalk server does not appear to be running on ";
			$message .= $config['host'] . ':' . $config['port'];
			$hasBeanstalk = ($e->getCode() != 503) ? true : false;
			$this->skipIf(!$hasBeanstalk, $message);
		}
		unset($conn);
	}

	public function testConnection() {
		$service = new Service();
		$result = $service->connect();
		$this->assertTrue($result);

		$this->service = &$service;
	}

	public function testChoose() {
		$service = &$this->service;

		$result = $service->choose('default');
		$this->assertEqual('USING', $result->status);
	}

	public function testReserveTimedOut() {
		$service = &$this->service;

		do {
			$result = $service->reserve(0);
			if($result->id) {
				$service->delete($result->id);
			}
		} while ($result->status == 'RESERVED');

		$this->assertEqual('TIMED_OUT', $result->status);
	}

	public function testPut() {
		$service = &$this->service;

		$result = $service->put('message', 0, 0, 0);
		$this->assertEqual('INSERTED', $result->status);
	}

	public function testReserveAndRelease() {
		$service = &$this->service;

		$result = $service->reserve(0);
		$this->assertEqual('RESERVED', $result->status);

		$result = $service->release($result->id, 0, 0);
		$this->assertEqual('RELEASED', $result->status);
	}

	public function testReserveAndDelete() {
		$service = &$this->service;

		$result = $service->reserve(0);
		$this->assertEqual('RESERVED', $result->status);

		$result = $service->delete($result->id);
		$this->assertEqual('DELETED', $result->status);
	}

	public function testListTubes() {
		$service = &$this->service;

		$result = $service->listTubes();
		$this->assertEqual('OK', $result->status);
	}

	public function testStatsTube() {
		$service = &$this->service;

		$result = $service->statsTube('default');
		$this->assertEqual('OK', $result->status);
	}

	public function testStats() {
		$service = &$this->service;

		$result = $service->stats();
		$this->assertEqual('OK', $result->status);
	}

}

?>