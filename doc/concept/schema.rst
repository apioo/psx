
Schema
======

Abstract
--------

This chapter explains more detailed how you can define a schema for your
data

Definition
----------

In the following we will define a complex sample schema

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
                ->setPrototype(Property::getString('tag'));
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
following we show the output of the schema transformed into a XSD or JsonSchema 
format.

XSD
^^^

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>
    <xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:tns="http://acme.com/schema" targetNamespace="http://acme.com/schema" elementFormDefault="qualified">
        <xs:element name="product">
            <xs:complexType>
                <xs:sequence>
                    <xs:element name="id" type="xs:integer" minOccurs="0" maxOccurs="1"/>
                    <xs:element name="title" type="tns:type49f11e3b13c41b5bda2c83f4f5ee5bd2" minOccurs="1" maxOccurs="1"/>
                    <xs:element name="price" type="tns:type48604a07011c0d27dba74bc0df93c602" minOccurs="1" maxOccurs="1"/>
                    <xs:element name="customer" type="tns:type044c12e289e7c3e50cefeb524544bc8d" minOccurs="1" maxOccurs="1"/>
                    <xs:element name="options" type="tns:type6697819882c5c09cfdd3cb1fea39a9fc" minOccurs="0" maxOccurs="unbounded"/>
                    <xs:element name="tags" type="xs:string" minOccurs="0" maxOccurs="unbounded"/>
                    <xs:element name="duration" type="xs:duration" minOccurs="0" maxOccurs="1"/>
                    <xs:element name="created" type="xs:dateTime" minOccurs="0" maxOccurs="1"/>
                </xs:sequence>
            </xs:complexType>
        </xs:element>
        <xs:simpleType name="type49f11e3b13c41b5bda2c83f4f5ee5bd2">
            <xs:restriction base="xs:string">
                <xs:pattern value="[A-z]+"/>
            </xs:restriction>
        </xs:simpleType>
        <xs:simpleType name="type48604a07011c0d27dba74bc0df93c602">
            <xs:restriction base="xs:float"/>
        </xs:simpleType>
        <xs:complexType name="type044c12e289e7c3e50cefeb524544bc8d">
            <xs:sequence>
                <xs:element name="name" type="tns:type49f11e3b13c41b5bda2c83f4f5ee5bd2" minOccurs="1" maxOccurs="1"/>
            </xs:sequence>
        </xs:complexType>
        <xs:complexType name="type6697819882c5c09cfdd3cb1fea39a9fc">
            <xs:sequence>
                <xs:element name="name" type="tns:type49f11e3b13c41b5bda2c83f4f5ee5bd2" minOccurs="1" maxOccurs="1"/>
                <xs:element name="price" type="tns:type48604a07011c0d27dba74bc0df93c602" minOccurs="1" maxOccurs="1"/>
            </xs:sequence>
        </xs:complexType>
    </xs:schema>


JsonSchema
^^^^^^^^^^

.. code-block:: json

    {
        "$schema": "http:\/\/json-schema.org\/draft-04\/schema#",
        "id": "http:\/\/acme.com\/schema",
        "type": "object",
        "definitions": {
            "ref044c12e289e7c3e50cefeb524544bc8d": {
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
            "ref6697819882c5c09cfdd3cb1fea39a9fc": {
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
                "$ref": "#\/definitions\/ref044c12e289e7c3e50cefeb524544bc8d"
            },
            "options": {
                "type": "array",
                "items": {
                    "$ref": "#\/definitions\/ref6697819882c5c09cfdd3cb1fea39a9fc"
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
