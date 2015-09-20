<?php

/**
 * This file is part of the JSend package.
 *
 * @author Michael Webbers <michael@webbers.io>
 * @copyright Copyright (c) 2015, Michael Webbers
 * @license http://opensource.org/licenses/Apache-2.0 Apache v2 License
 * @version 1.0.0
 */

namespace MWebbers\JSend\Tests;

use MWebbers\JSend\JSend;

/**
 * The JSend test class.
 *
 * @since 1.0.0
 * @see http://labs.omniti.com/labs/jsend
 */
class JSendTest extends \PHPUnit_Framework_TestCase
{
	const ERROR_MESSAGE = 'Internal Server error.';
	const ERROR_CODE = 500;

	const JSON_SUCCESS = '{"status":"success","data":{"post":{"id":1,"name":"test"}}}';
	const JSON_FAIL = '{"status":"fail","data":[]}';
	const JSON_ERROR = '{"status":"error","message":"Internal Server error.","code":500}';

	/** @var array The success data. */
	private $successData = ['post' => ['id' => 1, 'name' => 'test']];

	/** @var JSend The JSend success object. */
	private $success;

	/** @var JSend The JSend fail object. */
	private $fail;

	/** @var JSend The JSend error object. */
	private $error;

	public function setUp()
	{
		$this->success = new JSend(JSend::SUCCESS, $this->successData);
		$this->fail = new JSend(JSEND::FAIL, []);
		$this->error = new JSend(JSEND::ERROR, [], self::ERROR_MESSAGE, self::ERROR_CODE);
	}

	public function test__toString()
	{
		$this->assertJsonStringEqualsJsonString(self::JSON_SUCCESS, (string) $this->success);
		$this->assertJsonStringEqualsJsonString(self::JSON_FAIL, (string) $this->fail);
		$this->assertJsonStringEqualsJsonString(self::JSON_ERROR, (string) $this->error);
	}

	public function test__toArray()
	{
		$this->assertEquals(['status' => JSend::SUCCESS, 'data' => $this->successData], $this->success->__toArray());
		$this->assertEquals(['status' => JSend::FAIL, 'data' => []], $this->fail->__toArray());
		$this->assertEquals(['status' => JSend::ERROR, 'message' => self::ERROR_MESSAGE, 'code' => self::ERROR_CODE], $this->error->__toArray());
	}

	public function testSuccess()
	{
		$this->assertEquals($this->success, JSend::success($this->successData));
	}

	public function testFail()
	{
		$this->assertEquals($this->fail, JSend::fail([]));
	}

	public function testError()
	{
		$this->assertEquals($this->error, JSend::error(self::ERROR_MESSAGE, self::ERROR_CODE, []));
	}

	public function testDecode()
	{
		$this->assertEquals($this->success, JSend::decode(self::JSON_SUCCESS));
		$this->assertEquals($this->fail, JSend::decode(self::JSON_FAIL));
		$this->assertEquals($this->error, JSend::decode(self::JSON_ERROR));
	}

	public function testEncode()
	{
		$this->assertJsonStringEqualsJsonString(self::JSON_SUCCESS, $this->success->encode());
		$this->assertJsonStringEqualsJsonString(self::JSON_FAIL, $this->fail->encode());
		$this->assertJsonStringEqualsJsonString(self::JSON_ERROR, $this->error->encode());
	}

	public function testIsSuccess()
	{
		$this->assertTrue($this->success->isSuccess());
		$this->assertFalse($this->fail->isSuccess());
		$this->assertFalse($this->error->isSuccess());
	}

	public function testIsFail()
	{
		$this->assertFalse($this->success->isFail());
		$this->assertTrue($this->fail->isFail());
		$this->assertFalse($this->error->isFail());
	}

	public function testIsError()
	{
		$this->assertFalse($this->success->isError());
		$this->assertFalse($this->fail->isError());
		$this->assertTrue($this->error->isError());
	}

	public function testGetStatus()
	{
		$this->assertEquals(JSend::SUCCESS, $this->success->getStatus());
	}

	public function testSetStatus()
	{
		$this->success->setStatus(JSend::FAIL);
		$this->assertEquals(JSend::FAIL, $this->success->getStatus());
	}

	public function testGetData()
	{
		$this->assertEquals($this->successData, $this->success->getData());
	}

	public function testSetData()
	{
		$this->success->setData();
		$this->assertEmpty($this->success->getData());
	}

	public function testGetMessage()
	{
		$this->assertEquals(self::ERROR_MESSAGE, $this->error->getMessage());
	}

	public function testSetMessage()
	{
		$this->error->setMessage('');
		$this->assertEmpty($this->error->getMessage());
	}

	public function testGetCode()
	{
		$this->assertEquals(500, $this->error->getCode());
	}

	public function testSetCode()
	{
		$this->error->setCode();
		$this->assertNull($this->error->getCode());
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSend()
	{
        $this->expectOutputString((string) $this->success);
		$this->success->send();
	}
}
