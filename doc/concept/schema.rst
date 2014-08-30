
Schema
======

Abstract
--------

This chapter explains more detailed how you can define an schema for your
data

Definition
----------

In the following we will define an complex sample schema which show cases howto
create an schema

.. code-block:: php

    <?php

    namespace Acme\Schema;

    use PSX\Data\SchemaAbstract;
    use PSX\Data\Schema\Property;

    class Product extends SchemaAbstract
    {
        public function getDefinition()
        {
            $sb = $this->getSchemaBuilder('product');
            $sb->integer('id');
            $sb->string('title')
                ->setPattern('[A-z]+')
                ->setRequired(true);
            $sb->float('price')
                ->setMin(0)
                ->setRequired(true);
            $sb->complexType($this->getSchema('Acme\Schema\Customer'))
                ->setRequired(true);
            $sb->arrayType('options')
                ->setPrototype($this->getSchema('Acme\Schema\Option'));
            $sb->arrayType('tags')
                ->setPrototype(new Property\String('tag'));
            $sb->duration('duration');
            $sb->dateTime('created');

            return $sb->getProperty();
        }
    }

.. code-block:: php

    <?php

    namespace Acme\Schema;

    use PSX\Data\SchemaAbstract;
    use PSX\Data\Schema\Property;

    class Customer extends SchemaAbstract
    {
        public function getDefinition()
        {
            $sb = $this->getSchemaBuilder('customer');
            $sb->string('name')
                ->setPattern('[A-z]+')
                ->setRequired(true);

            return $sb->getProperty();
        }
    }

.. code-block:: php

    <?php

    namespace Acme\Schema;

    use PSX\Data\SchemaAbstract;
    use PSX\Data\Schema\Property;

    class Option extends SchemaAbstract
    {
        public function getDefinition()
        {
            $sb = $this->getSchemaBuilder('option');
            $sb->string('name')
                ->setPattern('[A-z]+')
                ->setRequired(true);
            $sb->float('price')
                ->setMin(0)
                ->setRequired(true);

            return $sb->getProperty();
        }
    }

This would match the following JSON request

.. code-block:: json

    {
        "id": 1,
        "title": "product",
        "price": 12.34,
        "customer": {
            "name": "customer"
        },
        "options": [{
            "name": "foo",
            "price": 10.10
        },{
            "name": "bar",
            "price": 10.20
        }],
        "tags": ["product", "fast", "cheap"],
        "duration": "PT1M",
        "created": "2014-08-07"
    }

Generate
--------

It is possible to generate different kind of output based on the schema. In the
following we show the output of the schema transformed into an XSD or JsonSchema 
format.

XSD
^^^

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>
    <xs:schema xmlns="http://acme.com/schema" xmlns:xs="http://www.w3.org/2001/XMLSchema" targetNamespace="http://acme.com/schema" elementFormDefault="qualified">
        <xs:element name="product">
            <xs:complexType>
                <xs:sequence>
                    <xs:element name="id" type="xs:integer" minOccurs="0" maxOccurs="1"/>
                    <xs:element name="title" type="title" minOccurs="1" maxOccurs="1"/>
                    <xs:element name="price" type="price" minOccurs="1" maxOccurs="1"/>
                    <xs:element name="customer" type="customer" minOccurs="1" maxOccurs="1"/>
                    <xs:element name="options" type="options" minOccurs="0" maxOccurs="1"/>
                    <xs:element name="tags" type="tags" minOccurs="0" maxOccurs="1"/>
                    <xs:element name="duration" type="xs:duration" minOccurs="0" maxOccurs="1"/>
                    <xs:element name="created" type="xs:dateTime" minOccurs="0" maxOccurs="1"/>
                </xs:sequence>
            </xs:complexType>
        </xs:element>
        <xs:simpleType name="title">
            <xs:restriction base="xs:string">
                <xs:pattern value="[A-z]+"/>
            </xs:restriction>
        </xs:simpleType>
        <xs:simpleType name="price">
            <xs:restriction base="xs:float"/>
        </xs:simpleType>
        <xs:complexType name="customer">
            <xs:sequence>
                <xs:element name="name" type="name" minOccurs="1" maxOccurs="1"/>
            </xs:sequence>
        </xs:complexType>
        <xs:simpleType name="name">
            <xs:restriction base="xs:string">
                <xs:pattern value="[A-z]+"/>
            </xs:restriction>
        </xs:simpleType>
        <xs:complexType name="options">
            <xs:sequence>
                <xs:element name="option" type="option" minOccurs="0" maxOccurs="unbounded"/>
            </xs:sequence>
        </xs:complexType>
        <xs:complexType name="option">
            <xs:sequence>
                <xs:element name="name" type="name" minOccurs="1" maxOccurs="1"/>
                <xs:element name="price" type="price" minOccurs="1" maxOccurs="1"/>
            </xs:sequence>
        </xs:complexType>
        <xs:complexType name="tags">
            <xs:sequence>
                <xs:element name="tag" type="xs:string" minOccurs="0" maxOccurs="unbounded"/>
            </xs:sequence>
        </xs:complexType>
    </xs:schema>

JsonSchema
^^^^^^^^^^

.. code-block:: json

    {
      "id": "http:\/\/acme.com\/schema",
      "type": "object",
      "properties": {
        "id": {
          "type": "integer"
        },
        "title": {
          "type": "string",
          "pattern": "[A-z]+"
        },
        "price": {
          "type": "number"
        },
        "customer": {
          "type": "object",
          "properties": {
            "name": {
              "type": "string",
              "pattern": "[A-z]+"
            }
          },
          "required": [
            "name"
          ],
          "additionalProperties": false
        },
        "options": {
          "type": "array",
          "items": {
            "type": "object",
            "properties": {
              "name": {
                "type": "string",
                "pattern": "[A-z]+"
              },
              "price": {
                "type": "number"
              }
            },
            "required": [
              "name",
              "price"
            ],
            "additionalProperties": false
          }
        },
        "tags": {
          "type": "array",
          "items": {
            "type": "string"
          }
        },
        "duration": {
          "type": "string"
        },
        "created": {
          "type": "string"
        }
      },
      "required": [
        "title",
        "price",
        "customer"
      ],
      "additionalProperties": false
    }
