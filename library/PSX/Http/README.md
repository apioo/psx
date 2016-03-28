PSX Http
===

## About

This is a mutable version of the [PSR-7](http://www.php-fig.org/psr/psr-7/)
specification. It has almost the same API except that the with* methods are set*
methods which set the value on the object and dont return a new instance. It
contains also a simple client to send requests.

## Reasoning

At first we really appreciate the work which has gone into making the PSR-7
specification possible. But we are not satisfied with the design decision to
"model" the HTTP request/response immutable. The only way to express this is to
not implement the specification.

Immutability forces a design on your application you can i.e. not write
middlewares which simply accept a request and response object. They must be
designed in such a way that they must return the changed response. This is also
a problem for existing applications since there is no backwards compatible way
to upgrade such an application. So as long as the majority of frameworks does
not use PSR-7 we remain with this implementation.

## Usage

```php
<?php

$client   = new Client();
$request  = new GetRequest('http://google.com', ['Accept' => 'text/html']);
$response = $client->request($request);

if ($response->getStatusCode() == 200) {
    echo (string) $response->getBody();
} else {
    // something goes wrong
}

```

