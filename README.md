
# PSX Framework

## About

PSX is a framework written in PHP dedicated to build REST APIs. It is based on 
multiple components which cover many aspects of the API lifecycle. These 
components are independent of the framework and can also be used in another 
context. The following list contains the most notable packages:

- [psx/api](https://github.com/apioo/psx-api)
  Parse and generate API specification formats
- [psx/data](https://github.com/apioo/psx-data)
  Data processing library to read and write POPOs in different formats
- [psx/schema](https://github.com/apioo/psx-schema)
  Parse and generate data schema formats

More information about the PSX framework and the components at [phpsx.org](http://phpsx.org/).

## Requirements

> &gt;= PHP 7.4

## Installation

To install the full stack framework you can install the sample project which
creates a basic PSX project with a sample API.

    php composer.phar create-project psx/sample .

