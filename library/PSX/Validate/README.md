PSX Validate
===

## About

Validation library which validates data using a filter system. Through a
validator it is possible to validate specific fields or complex data structures.

## Usage

```php
<?php

$validator = new Validator([
    new Property('/title', Validate::TYPE_STRING, [new Filter\Alnum(), new Filter\Length(3, 255)]),
    new Property('/author/name', Validate::TYPE_STRING, [new Filter\Alnum(), new Filter\Length(3, 32)]),
]);

$data = <<<JSON
{
    "title": "foo",
    "author": {
        "name": "bar"
    },
    "date": "2016-03-28T23:27:00Z"
}
JSON;

// throws an exception if a field is not valid
$validator->validate(json_decode($data));

```
