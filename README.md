
## About

__PSX is an innovative PHP framework dedicated to build fully typed REST APIs.__

It helps to improve the API development process by providing the following features:

* Fully typed controller classes
* Client SDK generator
* [OpenAPI](https://www.openapis.org/) generator
* Generate model classes based on a [TypeSchema](https://typeschema.org/) specification
* Uses the Symfony [DI container](https://github.com/symfony/dependency-injection) component
* Works with Doctrine [DBAL](https://github.com/doctrine/dbal) and [migrations](https://github.com/doctrine/migrations)
* Type-safe database interaction
* Endpoint integration testing

More information about PSX at [phpsx.org](https://phpsx.org/).

## Installation

To install the framework you can simply install this demo API project.

```
composer create-project psx/psx .
```

## Getting started

This repository contains already a fully working demo API build with PSX which you can use as a starting point and to
better understand how PSX works. In the following we go based on the demo files through the important concepts of PSX.

## Controller

A controller is the entrypoint of your app which gets invoked by the framework. A controller is a simple PHP class which
contains attributes to make specific methods invokable. In the following example we have a simple controller with a
`getAll` and `create` method which gets invoked if a `GET` or `POST` request arrives at the `/population` endpoint s.

```php
class Population extends ControllerAbstract
{
    #[Get]
    #[Path('/population')]
    public function getAll(#[Query] ?int $startIndex = null, #[Query] ?int $count = null): Model\PopulationCollection
    {
        return $this->populationTable->getCollection($startIndex, $count);
    }

    #[Post]
    #[Path('/population')]
    public function create(#[Body] Model\Population $payload): Model\Message
    {
        $id = $this->populationService->create($payload);

        $message = new Model\Message();
        $message->setSuccess(true);
        $message->setMessage('Population record successfully created');
        $message->setId($id);
        return $message;
    }
}
```

One key concept of PSX is that the arguments of your exposed controller methods are mapped to values of the incoming
HTTP request, at the `getAll` method the `$startIndex` and `$count` parameter are mapped to a query parameter from the
HTTP request, at the `create` method the `$payload` parameter is mapped to the request body. If you are familiar with
[Spring](https://spring.io/) or [NestJS](https://nestjs.com/) you already know this approach.

PSX uses the symfony DI container, all controller classes are automatically loaded through auto-wiring. This means
you can simply define at the constructor all dependencies which are needed for your controller. Please take a look at
the [container.php](resources/container.php) if you want to customize which classes are loaded.

## SDK

One of the greatest feature of PSX is that it can automatically generate a client SDK for the API which you have build.
To generate the client SDK simply run the following command.

```
php bin/psx generate:sdk
```

This writes the SDK to the `output/` folder. By default, the command generates the TypeScript SDK. Based on the
controller defined above PSX would generate the following client SDK.

```
const client = new Client(...);

client.population().getAll(startIndex?: number, count?: number);
client.population().create(payload: Population);
```

The client then contains the same schemas which are also defined at the backend but converted to TypeScript. This means
you are using exactly the same schema at the backend and frontend. If you change your schema at the backend you can then
regenerate the SDK and you will directly see all problems with your new schema. In this sense PSX provides similar
features like [tRPC](https://trpc.io/) but in a language neutral way.

The `generate:sdk` command accepts as argument a format which defines the type of SDK which is generated. The following
list shows some supported formats.

* `client-php`
* `client-typescript`
* `spec-openapi`

## Model

To enable this SDK generation PSX needs to understand the structure of the incoming or outgoing JSON payload. This is
done by using DTO models for every argument and return type. PSX contains a model generator which allows you to generate
those models based on a [TypeSchema](https://typeschema.org/) specification. Please take a look at the
[typeschema.json](./resources/typeschema.json) file which contains the models for our demo API. You can generate all
models using the following command s.

```
php bin/psx generate:model
```

The command writes all models to the `src/Model` folder. You can then use those models at the controller classes.

## Service

PSX recommends to move your actual business logic into a separate service class. The controller then simply invokes
methods from your service. While this is not mandatory it improves your code quality since you can easily use this
service also in another context. All classes under the `service/` folder are automatically loaded thus you can specify
all dependencies through simple constructor injection.

## Migrations

PSX uses doctrine [migrations](https://github.com/doctrine/migrations/) which helps to manage your database schema. To
generate a new migration you can simply run s.

```
php bin/psx migrations:generate
```

This would create a new migration file at `src/Migrations`. You can then model your table schema at this migration file.
After this you can run the `migrate` command to execute all needed database changes s.

```
php bin/psx migrations:migrate
```

Please take a look at the doctrine [migrations](https://github.com/doctrine/migrations/) project for more information
how the migration system works.

## Table

PSX provides a command which generates table and row classes to interact with your database in a type-safe way. This
command should be executed after you have executed all your migrations.

```
php bin/psx generate:table
```

This command then writes all files to the `src/Table` folder.

Note in general we think that for API development an ORM is not needed, but it would be easy possible to integrate any
existing ORM into PSX.

## Tests

PSX provides a way to easily build an integration test for every controller endpoint. The following extract shows the
test which requests the `/population` endpoint and simply compares the JSON payload with an existing JSON structure. 

```php
public function testGetAll(): void
{
    $response = $this->sendRequest('/population', 'GET');

    $actual = (string) $response->getBody();
    $expect = file_get_contents(__DIR__ . '/resources/collection.json');

    $this->assertEquals(200, $response->getStatusCode(), $actual);
    $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
}
```

Through this you can easily build integration tests for every endpoint. Please take a look at the
`tests/Controller/PopulationTest.php` file to see the complete test case.

## Components

Besides the framework PSX is build on various PHP components. These components are independent of the framework and can
also be used in another context. The following list contains the most notable packages:

- [psx/api](https://github.com/apioo/psx-api)  
  Parse and generate API specification formats
- [psx/schema](https://github.com/apioo/psx-schema)  
  Parse and generate data schema formats
- [psx/data](https://github.com/apioo/psx-data)  
  Data processing library to read and write POPOs in different formats
- [psx/sql](https://github.com/apioo/psx-sql)  
  Generate type-safe PHP classes from your database
