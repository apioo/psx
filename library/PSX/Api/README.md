PSX Api
===

## About

Currently there are several API specification formats (RAML, Swagger, etc.)
which describe the functionality of an API endpoint. With this project we try to
unify the usage of different specification formats by providing model classes
which can be used to read and write API specifications. The following list
contains all supported parser and generator classes.

Parser | Description
------ | -----------
RAML | Parses a [RAML 0.8](http://raml.org/) specification
Annotation | Parses a class which contains annotations

Generator | Description
--------- | -----------
RAML | Generates a [RAML 0.8](http://raml.org/) specification
JsonSchema | Generates a [JsonSchema](http://json-schema.org/) which contains all schemas of the specification
Swagger | Generates a [Swagger 1.2](https://github.com/OAI/OpenAPI-Specification/blob/master/versions/1.2.md) specification
WSDL | Generates a [WSDL 1.1](https://www.w3.org/TR/wsdl) specification
Xsd | Generates a [XSD](https://www.w3.org/TR/xmlschema-0/) which contains all schemas of the specification

## Usage

The following is a simple showcase of the resource API so you get a basic
understanding how the model is designed.

```php
<?php

// reads the RAML specification and generates a resource object
$resource = Raml::fromFile('spec.raml', '/foo');

// returns the title
$resource->getTitle();

// returns available path parameters as PSX\Schema\SchemaInterface
$resource->getPathParameters();

// checks whether a specific request method is supported
$resource->hasMethod('POST');

// returns all allowed methods
$resource->getAllowedMethods();

// returns the available query parameters per method as PSX\Schema\SchemaInterface
$resource->getMethod('POST')->getQueryParameters();

// checks whether the method has a request specification
$resource->getMethod('POST')->hasRequest();

// returns the request body specification as PSX\Schema\SchemaInterface
$resource->getMethod('POST')->getRequest();

// checks whether the method has a response with the status code 201
$resource->getMethod('POST')->hasResponse(201);

// returns the response body specification as PSX\Schema\SchemaInterface
$resource->getMethod('POST')->getResponse(201);

// creates a new WSDL generator and generate a WSDL representation of this
// resource
$generator = new Generator\Wsdl('foo', 'http://acme.com/soap', 'http://acme.com/tns');

$wsdl = $generator->generate($resource);

```

## Roadmap

The following list contains generator and parser classes which we want to
support in the near future. In case you want to support a different format do
not hesitate to open an issue or pull request.

Parser

* [Raml 1.0](https://github.com/raml-org/raml-spec/blob/raml-10/versions/raml-10/raml-10.md/)
* [Swagger 1.2](https://github.com/OAI/OpenAPI-Specification/blob/master/versions/1.2.md)
* [Swagger 2.0](http://swagger.io/specification/)

Generator

* [Raml 1.0](https://github.com/raml-org/raml-spec/blob/raml-10/versions/raml-10/raml-10.md/)
* [Swagger 2.0](http://swagger.io/specification/)
* [WADL](https://www.w3.org/Submission/wadl/)

## Remark

Note the downside of this approach is that we can only support the common subset
of all supported specification formats. In case you need a specific feature of
a specification you probably want to use a dedicated reader/writer.

