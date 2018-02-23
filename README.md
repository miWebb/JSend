[![Build Status](https://scrutinizer-ci.com/g/miwebb/JSend/badges/build.png?b=master)](https://scrutinizer-ci.com/g/miwebb/JSend/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/miwebb/JSend/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/miwebb/JSend/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/miwebb/JSend/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/miwebb/JSend/?branch=master)

# JSend

The PHP [JSend](https://labs.omniti.com/labs/jsend) implementation.

## Examples

### Use

```PHP
require 'vendor/autoload.php';

use miWebb/JSend/JSend;
```

### Create

#### Success

```PHP
$jsend = new JSend('success', $data);
$jsend = new JSend(JSend::SUCCESS, $data);
$jsend = JSend::success($data);
```

#### Fail

```PHP
$jsend = new JSend('fail', $data);
$jsend = new JSend(JSend::FAIL, $data);
$jsend = JSend::fail($data);
```

#### Error

```PHP
$jsend = new JSend('error', $data, $message, $code);
$jsend = new JSend(JSend::ERROR, $data, $message, $code);
$jsend = JSend::error($message, $code, $data);
```

#### Decode

```PHP
try {
	$jsend = JSend::decode($json);
} catch(UnexpectedValueException $e) {
	// Error message
}
```

### Output

#### Variable

```PHP
$json = $jsend->decode();
$json = (string) $jsend;
```

#### Print

```PHP
$jsend->send();
```

### Methods

```PHP
$jsend = new JSend(JSend::SUCCESS, $data);
$jsend->__toString();
$jsend->toArray();
$jsend->encode();
$jsend->isSuccess();
$jsend->isFail();
$jsend->isError();
$jsend->getStatus();
$jsend->setStatus($status);
$jsend->getData();
$jsend->setData($data = null);
$jsend->getMessage();
$jsend->setMessage($message = null);
$jsend->getCode();
$jsend->setCode($code = null);
$jsend->send();
```

```PHP
JSend::success($data = null);
JSend::fail($data);
JSend::error($message, $code = null, $data = null);
JSend::decode($input);
```
