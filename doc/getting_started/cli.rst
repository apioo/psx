
CLI
===

PSX provides several commands which help to develop and debug PSX applications. 
In the following we will describe the available commands:

debug
-----

`debug:jsonschema`
^^^^^^^^^^^^^^^^^^

Parses the json schema and prints informations about the parsed schema. Can be
used to debug json schemas.

.. code::

    $ vendor/bin/psx debug:jsonschema schema.json

`debug:raml`
^^^^^^^^^^^^

Parses the raml schema and prints informations about the parsed schema. Can be
used to debug raml schemas.

.. code::

    $ vendor/bin/psx debug:raml schema.json

generate
--------

`generate:api`
^^^^^^^^^^^^^^

Generates a new api controller in the source folder under the provided 
namespace. I.e. the following command would create the file `src/Acme/Foo.php`

.. code::

    $ vendor/bin/psx generate:api Acme\Foo connection,http

`generate:bootstrap_cache`
^^^^^^^^^^^^^^^^^^^^^^^^^^

Generates a bootstrap cache file in the cache folder. This file includes common
used classes which can be used to improve the performance of your application. 
Note this is only needed if you are not using a byte code cache (PHP < 5.5)

.. code::

    $ vendor/bin/psx generate:bootstrap_cache

`generate:command`
^^^^^^^^^^^^^^^^^^

Generates a new command in the source folder under the provided namespace. I.e. 
the following command would create the file `src/Acme/Foo.php`

.. code::

    $ vendor/bin/psx generate:command Acme\Foo connection,http

`generate:controller`
^^^^^^^^^^^^^^^^^^^^^

Generates a new controller in the source folder under the provided namespace. 
I.e. the following command would create the file `src/Acme/Foo.php`

.. code::

    $ vendor/bin/psx generate:controller Acme\Foo connection,http

`generate:schema`
^^^^^^^^^^^^^^^^^

Generates a new schema controller in the source folder under the provided 
namespace. I.e. the following command would create the file `src/Acme/Foo.php`

.. code::

    $ vendor/bin/psx generate:schema Acme\Foo connection,http

`generate:table`
^^^^^^^^^^^^^^^^

Generates a table class based on an actual table. The sql_ credentials must be 
provided in the `configuration.php` to use this command

.. code::

    $ vendor/bin/psx generate:table Acme\Foo table_name


`generate:view`
^^^^^^^^^^^^^^^

Generates a new view controller in the source folder under the provided 
namespace. I.e. the following command would create the file `src/Acme/Foo.php`

.. code::

    $ vendor/bin/psx generate:view Acme\Foo connection,template

schema
------

`schema:jsonschema`
^^^^^^^^^^^^^^^^^^^

Prints the json schema of a provided route

.. code::

    $ vendor/bin/psx schema:jsonschema /foo

`schema:raml`
^^^^^^^^^^^^^

Prints the raml schema of a provided route

.. code::

    $ vendor/bin/psx schema:raml /foo

`schema:swagger`
^^^^^^^^^^^^^^^^

Prints the swagger schema of a provided route

.. code::

    $ vendor/bin/psx schema:swagger /foo

`schema:wsdl`
^^^^^^^^^^^^^

Prints the wsdl schema of a provided route

.. code::

    $ vendor/bin/psx schema:wsdl /foo

`schema:xsd`
^^^^^^^^^^^^

Prints the xsd schema of a provided route

.. code::

    $ vendor/bin/psx schema:xsd /foo
