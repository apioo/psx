PSX Json
===

## About

Library which contains classes to handle JSON data.

## Usage

```php
<?php

$json = <<<JSON
{
    "bar": {
        "foo": "lorem ipsum"
    }
}
JSON;

// decode json
$data = Parse::decode($json);

// evaluate json pointer
$pointer = new Pointer('/bar/foo');
$value   = $pointer->evaluate($data); // lorem ipsum

// apply JSON patch operations
$patch = <<<JSON
[
     { "op": "add", "path": "/bar", "value": { "bar", "test" } }
]
JSON;

$patch = new Patch(Parse::decode($patch));
$data  = $patch->patch($data);

// encode json
echo Parser::encode($data);

```

### Web Token

The library contains also a class to produce and consume web tokens.

```php
<?php

$key = 'secret';

// build web token
$token = new WebSignature();
$token->setHeader(WebSignature::TYPE, 'JWT');
$token->setHeader(WebSignature::ALGORITHM, 'HS256');
$token->setClaim('Oo');

$compact = $token->getCompact($key);

// $compact contains the web token in compact form
// eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.T28.fTqVAlaLB0Ri4QS8-PfBjSuTSLgqwdmePBNZ-X2GBm4

// parse web token
$token = WebSignature::parse($compact);

// validates the token
$token->validate($key);

// access the values from the token
$token->getHeader(WebSignature::TYPE); // JWT
$token->getHeader(WebSignature::ALGORITHM); // HS256
$token->getClaim(); // Oo
$token->getSignature(); // fTqVAlaLB0Ri4QS8-PfBjSuTSLgqwdmePBNZ-X2GBm4

```

