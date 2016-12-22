
CLI
===

PSX provides several commands which help to rapidly develop and debug PSX 
applications. In the following we will describe the available commands:

API
---

It is possible to automatically generate an API controller from a specification.
The following command reads the `/acme/endpoint` and generates the appropriated
controller.

.. code::

    $ vendor/bin/psx api spec.raml php

Schema
------

Through the schema command it is possible to generate a POPO based on a 
jsonschema specification.

.. code::

    $ vendor/bin/psx schema schema.json php
