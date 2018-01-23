<?php

/**
 * This file is part of the JSend package.
 *
 * @author Michael Webbers <michael@webbers.io>
 * @copyright Copyright (c) 2015, Michael Webbers
 * @license http://opensource.org/licenses/Apache-2.0 Apache v2 License
 * @version 1.0.0
 */

namespace miWebb\JSend;

use PHPUnit\Framework\TestCase;

/**
 * The JSend test class.
 *
 * @since 1.0.0
 * @see http://labs.omniti.com/labs/jsend
 */
class JSendTest extends TestCase
{
	const ERROR_MESSAGE = 'Internal Server error.';
	const ERROR_CODE = 500;

	const JSON_SUCCESS = '{"status":"success","data":{"post":{"id":1,"name":"test"}}}';
	const JSON_NULL_SUCCESS = '{"status":"success","data":null}';
	const JSON_FAIL = '{"status":"fail","data":[]}';
	const JSON_NULL_FAIL = '{"status":"fail","data": null}';
	const JSON_ERROR = '{"status":"error","message":"Internal Server error.","code":500,"data":{"post":{"id":1,"name":"test"}}}';
	const JSON_NULL_ERROR = '{"status":"error","message":"Internal Server error.","code":500,"data":null}';

	/** @var array The data. */
	private $data = ['post' => ['id' => 1, 'name' => 'test']];
	private $nullData = null;

	/** @var JSend The JSend success object. */
	private $success;

	/** @var JSend The JSend success object with no data. */
	private $nullSuccess;

	/** @var JSend The JSend fail object. */
	private $fail;

	/** @var JSend The JSend error object. */
	private $error;

	public function setUp()
	{
		$this->success = new JSend(JSend::SUCCESS, $this->data);
		$this->nullSuccess = new JSend(JSend::SUCCESS, $this->nullData);
		$this->fail = new JSend(JSEND::FAIL, []);
		$this->nullFail = new JSend(JSend::FAIL, $this->nullData);
		$this->error = new JSend(JSEND::ERROR, $this->data, self::ERROR_MESSAGE, self::ERROR_CODE);
		$this->nullError = new JSend(JSend::ERROR, $this->nullData, self::ERROR_MESSAGE, self::ERROR_CODE);
	}

	public function test__toString()
	{
		$this->assertJsonStringEqualsJsonString(self::JSON_SUCCESS, (string) $this->success);
		$this->assertJsonStringEqualsJsonString(self::JSON_NULL_SUCCESS, (string) $this->nullSuccess);
		$this->assertJsonStringEqualsJsonString(self::JSON_FAIL, (string) $this->fail);
		$this->assertJsonStringEqualsJsonString(self::JSON_NULL_FAIL, (string) $this->nullFail);
		$this->assertJsonStringEqualsJsonString(self::JSON_ERROR, (string) $this->error);
		$this->assertJsonStringEqualsJsonString(self::JSON_NULL_ERROR, (string) $this->nullError);
	}

	public function testToArray()
	{
		$this->assertEquals(['status' => JSend::SUCCESS, 'data' => $this->data], $this->success->toArray());
		$this->assertEquals(['status' => JSend::SUCCESS, 'data' => $this->nullData], $this->nullSuccess->toArray());
		$this->assertEquals(['status' => JSend::FAIL, 'data' => []], $this->fail->toArray());
		$this->assertEquals(['status' => JSend::FAIL, 'data' => $this->nullData], $this->nullFail->toArray());
		$this->assertEquals(['status' => JSend::ERROR, 'message' => self::ERROR_MESSAGE, 'code' => self::ERROR_CODE, 'data' => $this->data], $this->error->toArray());
		$this->assertEquals(['status' => JSend::ERROR, 'message' => self::ERROR_MESSAGE, 'code' => self::ERROR_CODE, 'data' => $this->nullData], $this->nullError->toArray());
	}

	public function testSuccess()
	{
		$this->assertEquals($this->success, JSend::success($this->data));
		$this->assertEquals($this->nullSuccess, JSend::success($this->nullData));
	}

	public function testFail()
	{
		$this->assertEquals($this->fail, JSend::fail([]));
		$this->assertEquals($this->nullFail, JSend::fail($this->nullData));
	}

	public function testError()
	{
		$this->assertEquals($this->error, JSend::error(self::ERROR_MESSAGE, self::ERROR_CODE, $this->data));
		$this->assertEquals($this->nullError, JSend::error(self::ERROR_MESSAGE, self::ERROR_CODE, $this->nullData));
	}

	public function testDecode()
	{
		$this->assertEquals($this->success, JSend::decode(self::JSON_SUCCESS));
		$this->assertEquals($this->nullSuccess, JSend::decode(self::JSON_NULL_SUCCESS));
		$this->assertEquals($this->fail, JSend::decode(self::JSON_FAIL));
		$this->assertEquals($this->nullFail, JSend::decode(self::JSON_NULL_FAIL));
		$this->assertEquals($this->error, JSend::decode(self::JSON_ERROR));
		$this->assertEquals($this->nullError, JSend::decode(self::JSON_NULL_ERROR));
	}

	/**
	 * @expectedException \UnexpectedValueException
	 * @expectedExceptionMessage JSend JSON can not be decoded.
	 */
	public function testDecodeCannotDecode()
	{
		JSend::decode('');
	}

	/**
	 * @expectedException \UnexpectedValueException
	 * @expectedExceptionMessage JSend objects require a status.
	 */
	public function testDecodeNoStatus()
	{
		JSend::decode('{}');
	}

	/**
	 * @expectedException \UnexpectedValueException
	 * @expectedExceptionMessage JSend success objects require data.
	 */
	public function testDecodeSuccesRequireData()
	{
		JSend::decode('{"status": "success"}');
	}

	/**
	 * @expectedException \UnexpectedValueException
	 * @expectedExceptionMessage JSend fail objects require data.
	 */
	public function testDecodeFailRequireData()
	{
		JSend::decode('{"status": "fail"}');
	}

	/**
	 * @expectedException \UnexpectedValueException
	 * @expectedExceptionMessage JSend error objects require a message.
	 */
	public function testDecodeErrorRequireMessage()
	{
		JSend::decode('{"status": "error"}');
	}

	/**
	 * @expectedException \UnexpectedValueException
	 * @expectedExceptionMessage test is not a valid JSend status.
	 */
	public function testDecodeExistingStatus()
	{
		JSend::decode('{"status": "test"}');
	}

	public function testEncode()
	{
		$this->assertJsonStringEqualsJsonString(self::JSON_SUCCESS, $this->success->encode());
		$this->assertJsonStringEqualsJsonString(self::JSON_NULL_SUCCESS, $this->nullSuccess->encode());
		$this->assertJsonStringEqualsJsonString(self::JSON_FAIL, $this->fail->encode());
		$this->assertJsonStringEqualsJsonString(self::JSON_NULL_FAIL, $this->nullFail->encode());
		$this->assertJsonStringEqualsJsonString(self::JSON_ERROR, $this->error->encode());
		$this->assertJsonStringEqualsJsonString(self::JSON_NULL_ERROR, $this->nullError->encode());
	}

	public function testIsSuccess()
	{
		$this->assertTrue($this->success->isSuccess());
		$this->assertTrue($this->nullSuccess->isSuccess());
		$this->assertFalse($this->fail->isSuccess());
		$this->assertFalse($this->nullFail->isSuccess());
		$this->assertFalse($this->error->isSuccess());
		$this->assertFalse($this->nullError->isSuccess());
	}

	public function testIsFail()
	{
		$this->assertFalse($this->success->isFail());
		$this->assertFalse($this->nullSuccess->isFail());
		$this->assertTrue($this->fail->isFail());
		$this->assertTrue($this->nullFail->isFail());
		$this->assertFalse($this->error->isFail());
		$this->assertFalse($this->nullError->isFail());
	}

	public function testIsError()
	{
		$this->assertFalse($this->success->isError());
		$this->assertFalse($this->nullSuccess->isError());
		$this->assertFalse($this->fail->isError());
		$this->assertFalse($this->nullFail->isError());
		$this->assertTrue($this->error->isError());
		$this->assertTrue($this->nullError->isError());
	}

	public function testGetStatus()
	{
		$this->assertEquals(JSend::SUCCESS, $this->success->getStatus());
		$this->assertEquals(JSend::SUCCESS, $this->nullSuccess->getStatus());
	}

	/**
	 * @depends testGetStatus
	 */
	public function testSetStatus()
	{
		$this->success->setStatus(JSend::FAIL);
		$this->nullSuccess->setStatus(JSend::FAIL);
		$this->assertEquals(JSend::FAIL, $this->success->getStatus());
		$this->assertEquals(JSend::FAIL, $this->nullSuccess->getStatus());
	}

	/**
	 * @depends testSetStatus
	 * @expectedException \UnexpectedValueException
	 * @expectedExceptionMessage test is not a valid JSend status.
	 */
	public function testExceptionSetStatus()
	{
		$this->success->setStatus('test');
		$this->nullSuccess->setStatus('test');
	}

	public function testGetData()
	{
		$this->assertEquals($this->data, $this->success->getData());
		$this->assertEquals($this->nullData, $this->nullSuccess->getData());
	}

	/**
	 * @depends testGetData
	 */
	public function testSetData()
	{
		$data = ['key' => 'value'];

		$this->success->setData($data);
		$this->nullSuccess->setData($this->nullData);
		$this->assertEquals($data, $this->success->getData());
		$this->assertEquals($this->nullData, $this->nullSuccess->getData());
	}

	/**
	 * @depends testGetData
	 */
	public function testSetDataEmpty()
	{
		$this->success->setData();
		$this->assertEmpty($this->success->getData());
		$this->nullSuccess->setData();
		$this->assertEmpty($this->nullSuccess->getData());
	}

	public function testGetMessage()
	{
		$this->assertEquals(self::ERROR_MESSAGE, $this->error->getMessage());
		$this->assertEquals(self::ERROR_MESSAGE, $this->nullError->getMessage());
	}

	/**
	 * @depends testGetMessage
	 */
	public function testSetMessage()
	{
		$this->error->setMessage('');
		$this->assertEmpty($this->error->getMessage());
		$this->nullError->setMessage('');
		$this->assertEmpty($this->nullError->getMessage());
	}

	public function testGetCode()
	{
		$this->assertEquals(500, $this->error->getCode());
		$this->assertEquals(500, $this->nullError->getCode());
	}

	/**
	 * @depends testGetCode
	 */
	public function testSetCode()
	{
		$this->error->setCode();
		$this->assertNull($this->error->getCode());
		$this->nullError->setCode();
		$this->assertNull($this->nullError->getCode());
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSendSuccess()
	{
		$this->expectOutputString((string) $this->success);
		$this->success->send();
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSendNullSuccess()
	{
		$this->expectOutputString((string) $this->nullSuccess);
		$this->nullSuccess->send();
	}

	/**
	 * @depends testSendSuccess
	 * @runInSeparateProcess
	 */
	public function testSendFail()
	{
		$this->expectOutputString((string) $this->fail);
		$this->fail->send();
	}

	/**
	 * @depends testSendSuccess
	 * @runInSeparateProcess
	 */
	public function testSendNullFail()
	{
		$this->expectOutputString((string) $this->nullFail);
		$this->nullFail->send();
	}

	/**
	 * @depends testSendFail
	 * @runInSeparateProcess
	 */
	public function testSendError()
	{
		$this->expectOutputString((string) $this->error);
		$this->error->send();
	}

	/**
	 * @depends testSendFail
	 * @runInSeparateProcess
	 */
	public function testSendNullError()
	{
		$this->expectOutputString((string) $this->nullError);
		$this->nullError->send();
	}
}
