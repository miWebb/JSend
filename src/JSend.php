<?php

/**
 * This file is part of the JSend package.
 *
 * @author Michael Webbers <michael@webbers.io>
 * @copyright Copyright (c) 2015, Michael Webbers
 * @license http://opensource.org/licenses/Apache-2.0 Apache v2 License
 * @version 1.0.0
 */

namespace MWebbers\JSend;

/**
 * The JSend class.
 *
 * @since 1.0.0
 * @see http://labs.omniti.com/labs/jsend
 */
class JSend
{
	const SUCCESS = 'success';
	const FAIL = 'fail';
	const ERROR = 'error';

	/** @var string The status. */
	private $status;

	/** @var array The data. */
	private $data;

	/** @var string|null The message. */
	private $message;

	/** @var int|null The code. */
	private $code;

	/**
	 * Construct a JSend object with the given status, data, message and code.
	 *
	 * @param string $status
	 * @param array|null $data = null
	 * @param string|null $message = null
	 * @param int|null $code = null
	 * @throws \UnexpectedValueException
	 */
	public function __construct($status, array $data = null, $message = null, $code = null)
	{
		$this->setStatus($status);
		$this->setData($data);
		$this->setMessage($message);
		$this->setCode($code);
	}

	/**
	 * Returns the string representation of the object.
	 *
	 * @return string the string representation of the object.
	 */
	public function __toString()
	{
		return $this->encode();
	}

	/**
	 * Returns the array representation of the object.
	 *
	 * @return array the array representation of the object.
	 */
	public function toArray()
	{
		$result = ['status' => $this->status];

		switch ($this->status) {
			case self::ERROR:
				$result['message'] = $this->message;

				if ($this->code !== null) {
					$result['code'] = $this->code;
				}

				if (!empty($this->data)) {
					$result['data'] = $this->data;
				}

				break;

			default:
				$result['data'] = $this->data;

				break;
		}

		return $result;
	}

	/**
	 * Returns a JSend succes object with the given data.
	 *
	 * @param array|null $data
	 * @return JSend a JSend succes object with the given data.
	 */
	public static function success(array $data = null)
	{
		return new self(self::SUCCESS, $data);
	}

	/**
	 * Returns a JSend fail object with the given data.
	 *
	 * @param array|null $data
	 * @return JSend a JSend fail object with the given data.
	 */
	public static function fail(array $data = null)
	{
		return new self(self::FAIL, $data);
	}

	/**
	 * Returns a JSend error object with the given message, code and data.
	 *
	 * @param string $message
	 * @param int|null $code = null
	 * @param array $data = null
	 * @return JSend a JSend error object with the given message, code and data.
	 */
	public static function error($message, $code = null, array $data = null)
	{
		return new self(self::ERROR, $data, $message, $code);
	}

	/**
	 * Returns the decoded JSend object.
	 *
	 * @param string $input
	 * @return JSend the decoded JSend object.
	 * @throws \UnexpectedValueException
	 */
	public static function decode($input)
	{
		$json = json_decode($input, true);

		if ($json === null) {
			throw new \UnexpectedValueException('JSend JSON can not be decoded.');
		}

		return self::decodeJson($json);
	}

	/**
	 * Returns the decoded JSend input.
	 *
	 * @param array $json
	 * @return JSend the decoded JSend input.
	 * @throws \UnexpectedValueException
	 */
	public static function decodeJson(array $json)
	{
		if (!array_key_exists('status', $json)) {
			throw new \UnexpectedValueException('JSend objects require a status.');
		}

		switch ($json['status']) {
			case self::SUCCESS:
				return self::decodeSucces($json);

			case self::FAIL:
				return self::decodeFail($json);

			case self::ERROR:
				return self::decodeError($json);
		}

		throw new \UnexpectedValueException($json['status'] . ' is not a valid JSend status.');
	}

	/**
	 * Returns the decoded jSend succes object.
	 *
	 * @param array $json
	 * @return JSend the decoded jSend succes object.
	 * @throws \UnexpectedValueException
	 */
	public static function decodeSucces(array $json)
	{
		if (!array_key_exists('data', $json)) {
			throw new \UnexpectedValueException('JSend success objects require data.');
		}

		return self::success($json['data']);
	}

	/**
	 * Returns the decoded jSend fail object.
	 *
	 * @param array $json
	 * @return JSend the decoded jSend fail object.
	 * @throws \UnexpectedValueException
	 */
	public static function decodeFail(array $json)
	{
		if (!array_key_exists('data', $json)) {
			throw new \UnexpectedValueException('JSend fail objects require data.');
		}

		return self::fail($json['data']);
	}

	/**
	 * Returns the decoded jSend error object.
	 *
	 * @param array $json
	 * @return JSend the decoded jSend error object.
	 * @throws \UnexpectedValueException
	 */
	public static function decodeError(array $json)
	{
		if (!array_key_exists('message', $json)) {
			throw new \UnexpectedValueException('JSend error objects require a message.');
		}

		$code = array_key_exists('code', $json) ? $json['code'] : null;
		$data = array_key_exists('data', $json) ? $json['data'] : null;

		return self::error($json['message'], $code, $data);
	}

	/**
	 * Returns the encoded JSend object.
	 *
	 * @return string the encoded JSend object.
	 */
	public function encode()
	{
		return json_encode($this->toArray(), \JSON_NUMERIC_CHECK);
	}

	/**
	 * Returns true if the status is success.
	 *
	 * @return bool true if the status is success.
	 */
	public function isSuccess()
	{
		return self::SUCCESS === $this->status;
	}

	/**
	 * Returns true if the status is fail.
	 *
	 * @return bool true if the status is fail.
	 */
	public function isFail()
	{
		return self::FAIL === $this->status;
	}

	/**
	 * Returns true if the status is error.
	 *
	 * @return bool true if the status is error.
	 */
	public function isError()
	{
		return self::ERROR === $this->status;
	}

	/**
	 * Returns the status.
	 *
	 * @return string the status.
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Set the status.
	 *
	 * @param string $status
	 * @return JSend $this
	 * @throws \UnexpectedValueException
	 */
	public function setStatus($status)
	{
		if ($status !== self::SUCCESS && $status !== self::FAIL && $status !== self::ERROR) {
			throw new \UnexpectedValueException($status . ' is not a valid JSend status.');
		}

		$this->status = $status;

		return $this;
	}

	/**
	 * Returns the data.
	 *
	 * @return array the data.
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Set the data.
	 *
	 * @param array|null $data = null
	 * @return JSend $this
	 */
	public function setData(array $data = null)
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * Returns the message.
	 *
	 * @return string|null the message.
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Set the message.
	 *
	 * @param string|null $message = null
	 * @return JSend $this
	 */
	public function setMessage($message = null)
	{
		$this->message = $message;

		return $this;
	}

	/**
	 * Returns the code.
	 *
	 * @return int|null the code.
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * Set the code.
	 *
	 * @param int|null $code = null
	 * @return JSend $this
	 */
	public function setCode($code = null)
	{
		$this->code = $code;

		return $this;
	}

	/**
	 * Sends the JSend object.
	 *
	 * @return void
	 */
	public function send()
	{
		header('Content-Type: application/json');
		echo (string) $this;
	}
}
