PSX Framework
===

## About

PSX is a framework written in PHP dedicated to build REST APIs. It is based on 
multiple components which cover many aspects of the API lifecycle. These 
components are independent of the framework and can also be used in another 
context. The following list contains the most notable packages:

- [psx/api](https://github.com/apioo/psx-api)
  Parse and generate API specification formats (i.e. OpenAPI, RAML)
- [psx/data](https://github.com/apioo/psx-data)
  Data processing library to read and write POPOs in different formats
- [psx/schema](https://github.com/apioo/psx-schema)
  Parse and generate data schema formats (i.e. JsonSchema)
- [psx/sql](https://github.com/apioo/psx-sql)
  Build complex data structures from relational databases
- [psx/framework](https://github.com/apioo/psx-framework)
  Engine of the PSX framework

More informations about the PSX framework and the components at
[phpsx.org](http://phpsx.org/).

## Requirements

> &gt;= PHP 7.0

## Installation

To install the full stack framework you can install the sample project which
creates a basic PSX project with a sample API.

    php composer.phar create-project psx/sample .

you can also download the current release from GitHub which already includes all
vendor libraries in case you can not use composer

    https://github.com/apioo/psx/releases

## Documentation

For documentation please take a look at [phpsx.org](http://phpsx.org/) or the 
[official manual](http://psx.readthedocs.org/)

[![Build Status](https://travis-ci.org/apioo/psx.png)](https://travis-ci.org/apioo/psx)
