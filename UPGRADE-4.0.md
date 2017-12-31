
# Upgrade from 3.x to 4.x

This upgrade contains not many and breaking changes and it is likely that you 
can upgrade your app easily to 4.0. Basically the biggest change is the 
introduction of a new environment engine concept which let you easily run PSX
in many different web server implementations. Because of that you need to change
your `index.php` code to:

```
<?php

require_once(__DIR__ . '/../vendor/autoload.php');

$container = require_once(__DIR__ . '/../container.php');

$engine      = new \PSX\Framework\Environment\WebServer\Engine();
$environment = new \PSX\Framework\Environment\Environment($container, $engine);

return $environment->serve();
```

By default we have a WebServer engine which should be used for classical web 
servers like Apache or Nginx. But we have also added support for:

* Swoole (https://github.com/swoole/swoole-src)
* Aerys (https://github.com/amphp/aerys).

Those web servers work more like i.e. NodeJs (Javascript) or Netty (Java) but
for PHP. This means PHP executes once the server script and all incoming 
requests are served from this context. This brings a huge speed improvement 
since PHP does not need to parse the code on every request. But it requires also 
more careful coding since you can easily introduce memory leaks. We think that
there is a great future in those implementations and thus wee add this system to 
be able to run PSX in many different server implementations.

Because of this change we have removed the following internal services:

* `console_reader`
* `dispatch_sender`
* `request_factory`
* `response_factory`
