
# PSX Framework

## About

PSX is a framework written in PHP dedicated to build REST APIs. It is based on multiple components which cover many
aspects of the API lifecycle. These components are independent of the framework and can also be used in another context.
The following list contains the most notable packages:

- [psx/api](https://github.com/apioo/psx-api)  
  Parse and generate API specification formats
- [psx/schema](https://github.com/apioo/psx-schema)  
  Parse and generate data schema formats
- [psx/data](https://github.com/apioo/psx-data)  
  Data processing library to read and write POPOs in different formats

More information about the PSX components at [phpsx.org](https://phpsx.org/).

## Requirements

> &gt;= PHP 8.0

## Installation

To install the framework you can install the sample project which creates a basic PSX project with a sample API.

    php composer.phar create-project psx/sample .

## Usage

This section describes the basic usage of the PSX framework.

### Controller

A controller in PSX always represents a resource. It has methods to handle `GET`, `POST`, `PUT`, `PATCH` and `DELETE`
requests through the `doGet`, `doPost`, `doPut`, `doPatch` and `doDelete` methods. If your controller implements one of
the `do*` method the framework can automatically generate an OpenAPI specification knowing that you have an endpoint
supporting this request method. In the following we take a look at an example controller from the sample project:

```php
<?php

namespace App\Api\Population;

use App\Model;
use App\Service;
use PSX\Api\Attribute\Incoming;
use PSX\Api\Attribute\Outgoing;
use PSX\Api\Attribute\QueryParam;
use PSX\Dependency\Attribute\Inject;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Http\Environment\HttpContextInterface;

class Collection extends ControllerAbstract
{
    #[Inject]
    private Service\Population $populationService;

    #[QueryParam(name: "startIndex", type: "integer")]
    #[QueryParam(name: "count", type: "integer")]
    #[Outgoing(code: 200, schema: Model\Collection::class)]
    protected function doGet(HttpContextInterface $context): mixed
    {
        return $this->populationService->getAll(
            (int) $context->getParameter('startIndex'),
            (int) $context->getParameter('count')
        );
    }

    #[Incoming(schema: Model\Population::class)]
    #[Outgoing(code: 200, schema: Model\Message::class)]
    protected function doPost(mixed $record, HttpContextInterface $context): Model\Message
    {
        $this->populationService->create($record);

        return new Model\Message(true, 'Create population successful');
    }
}

```

Through the `Inject` attribute you can access services from the DI container. The api attributes describe the
capabilities of your endpoint. In the controller we then use a separate service so that our business logic is
independent of our framework.

### DI

The DI container is a simple class where every method represents a service. The controller can then inject the fitting
service based on the return type of the method. So in our case we set as return type `Service\Population` and in the
controller above we also use `Service\Population` as type hint. Through this you can simply extend the services to your
needs and use those services in your controller.

```php
<?php

namespace App\Dependency;

use App\Service;
use App\Table;
use PSX\Framework\Dependency\DefaultContainer;

class Container extends DefaultContainer
{
    public function getPopulationService(): Service\Population
    {
        return new Service\Population(
            $this->get('table_manager')->getTable(Table\Population::class)
        );
    }
}

```

### Routing

The routing file is a simple PHP file which returns an array. To add a new route you simply need to add an entry to the
array and reference the fitting controller. The following is a sample routing file from the sample project:

```php
<?php

return [

    # API
    [['GET'], '/', App\Api\Index::class],
    [['ANY'], '/population', App\Api\Population\Collection::class],
    [['ANY'], '/population/:id', App\Api\Population\Entity::class],

    # tool controller
    [['ANY'], '/tool/discovery', \PSX\Framework\Controller\Tool\DiscoveryController::class],
    [['ANY'], '/tool/routing', \PSX\Framework\Controller\Tool\RoutingController::class],
    [['ANY'], '/tool/doc', \PSX\Framework\Controller\Tool\Documentation\IndexController::class],
    [['ANY'], '/tool/doc/:version/*path', \PSX\Framework\Controller\Tool\Documentation\DetailController::class],
    [['ANY'], '/tool/raml/:version/*path', \PSX\Framework\Controller\Generator\RamlController::class],
    [['ANY'], '/tool/openapi/:version/*path', \PSX\Framework\Controller\Generator\OpenAPIController::class],

];

```

Besides you app controllers PSX provides some tool controllers which help you to generate i.e. an OpenAPI specification.
If you like you can also add them to your routes file.

### Engine

Normally PHP apps are served through classical web servers like Apache or Nginx but there are currently also other ways
to run your app. We are very interested in those approaches and with PSX we have implemented an engine system to support
different runtimes. If we take a look at our `index.php` file:

```php
<?php

require_once(__DIR__ . '/../vendor/autoload.php');

$container = require_once(__DIR__ . '/../container.php');

$engine      = null; # adjust the engine class
$environment = \PSX\Framework\Environment\Environment::fromContainer($container, $engine);

$environment->serve();

```

If the `$engine` variable is null we use the default engine assuming the usage of a classical web server. If you want to
use a different web server like Amp, Swoole or RoadRunner you can use a specific engine. The following engines are
available:

* **Apache/NGINX**

  Classical web server like Apache or NGINX which uses either FCGI or includes
  PHP as module (Apache). This engine is included by default.

  * Class: `PSX\Engine\WebServer\Engine`

* **Amp**

  A HTTP server completely written in PHP

  * Github: https://github.com/amphp/http-server
  * Package: `psx/engine-amp`
  * Class: `PSX\Engine\Amp\Engine`

* **Swoole**

  A HTTP server written as PHP extension in C/C++

  * Github: https://github.com/swoole/swoole-src
  * Package: `psx/engine-swoole`
  * Class: `PSX\Engine\Swoole\Engine`

* **Roadrunner**

  A HTTP server written in GO

  * Github: https://github.com/spiral/roadrunner
  * Package: `psx/engine-roadrunner`
  * Class: `PSX\Engine\Roadrunner\Engine`
