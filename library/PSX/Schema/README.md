PSX Schema
===

## About

The schema library contains classes to model a schema definition. These model
objects can be generated from different sources. It is possible to traverse
through a schema based on arbitrary data and apply specific actions. The
following parser classes are available to produce such model objects.

Parser | Description
------ | -----------
JsonSchema | Parses a [JsonSchema](http://json-schema.org/) file
Popo | Parses the annotations of a class

The following generator classes are available to generate a specific output
based on a model object.

Generator | Description
--------- | -----------
HTML | Generates a HTML representation of the schema
JsonSchema | Generates a [JsonSchema](http://json-schema.org/) specification
Sample | Generates a sample format using provided data
XSD | Generates a [XSD](https://www.w3.org/TR/xmlschema-0/) specification

## Usage

```php

// read a schema from a JsonSchema file
$schema = JsonSchema::fromFile('schema.json');

// the schema traverse can be used traverse through arbitrary data based on the
// schema
$traverser = new SchemaTraverser();

// the visitor can take action for each visited value. The incoming visitor
// validates the data according to the schema
$visitor = new IncomingVisitor();

// now we traverse the data
$traverser->traverse($data, $schema, $visitor);

// we can generate a XSD representation from the schema
$generator = new Xsd('http://phpsx.org/tns');

echo $generator->generate($schema);

```
