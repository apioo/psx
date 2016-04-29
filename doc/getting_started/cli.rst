
CLI
===

PSX provides several commands which help to rapidly develop and debug PSX 
applications. In the following we will describe the available commands:

Generate
--------

It is possible to automatically generate an API controller from a specification.
The following command reads the `/acme/endpoint` and generates the appropriated
controller under the `Acme\\News` namespace.

.. code::

    $ vendor/bin/psx generate spec.raml /acme/endpoint Acme\News

Resource
--------

You can generate the schema format of each API endpoint in different formats.
This example generates a swagger specifiction for the `/acme/endpoint` endpoint.

.. code::

    $ vendor/bin/psx resource /acme/endpoint swagger

The following formats are available: `raml`, `swagger`, `wsdl`, `xsd`, 
`jsonschema`, `php`, `serialize`.

Schema
------

Through the schema command it is possible to generate a POPO based on a 
jsonschema specification.

.. code::

    $ vendor/bin/psx schema jsonschema schema.json php
