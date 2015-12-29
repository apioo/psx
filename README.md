PSX Framework
===

## About

PSX is a framework written in PHP to create RESTful APIs. It provides tools to 
handle routing, API versioning, data transformation, authentication, 
documentation and testing. With PSX you can easily build a REST API around an 
existing application or create a new one from scratch. More informations at
[phpsx.org](http://phpsx.org/)

## Requirements

> &gt;= PHP 5.4 or HHVM 3.2.0

## Installation

The best option is to install the PSX sample project via composer

    php composer.phar create-project psx/sample .

it is also possible to require PSX as dependency in your composer.json

    "psx/psx": "~1.1"

you can also download a current release from github which already includes all
vendor libraries in case composer is not available

    https://github.com/k42b3/psx/releases

check out the [get started](http://phpsx.org/get-started) guide for more 
detailed instructions.

## Documentation

For documentation please take a look at [phpsx.org](http://phpsx.org/) or the 
[official manual](http://psx.readthedocs.org/)

[![Build Status](https://travis-ci.org/k42b3/psx.png)](https://travis-ci.org/k42b3/psx)
