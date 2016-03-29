PSX Uri
===

## About

Library which contains value objects to represent URI, URL and URNs. The value
objects are immutable so in case you change a value through a with* method you
get a new instance of that object. There is also a uri resolver class to resolve
a uri against a base uri.

## Usage

```php
<?php

$uri = new Uri('/bar?foo=bar');

$uri->getPath(); // /bar
$uri->getQuery(); // foo=bar

$uri = $uri->withScheme('https');
$uri = $uri->withScheme('foo.com');

echo $uri->toString(); // https://foo.com/bar?foo=bar

// the url object validates whether a scheme and host is available thus it is
// a valid url
$url = new Url($uri->toString());

// a urn provides additional getter to get the urn specific components. A urn
// must start with urn:
$urn = new Urn('urn:uuid:6e8bc430-9c3a-11d9-9669-0800200c9a66');

$urn->getNid(); // uuid
$urn->getNss(); // 6e8bc430-9c3a-11d9-9669-0800200c9a66
```
