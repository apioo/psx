
Installation
============

PSX uses composer as dependency manager so in order to install PSX composer must
be installed on your system. More informations howto install composer at 
https://getcomposer.org/. In case you cant install composer PSX has also an
pre-packaged release which already includes all dependencies which you can 
download at https://github.com/k42b3/psx/releases.

Then you can install the sample project with the following command: 

.. code::

    $ php composer.phar create-project psx/sample .

The sample project contains an sample API and all classes which you need to
start with PSX.

Configuration
-------------

The main configuration is defined in the file :file:`configuration.php` 
which is an simple php array with key value pairs. You must change the key 
"psx_url" so that it points to the psx public root. All other entries are 
optional.

.. literalinclude:: ../../configuration.php
   :language: php

If your application needs database access you can enter the credentials in the
:code:`psx_sql_*` keys. The connection service provides and Doctrine DBAL 
connection which you can use in your application.

Routing
-------

In order to make an controller accessible you have to define a route in your 
:file:`routes` file which invokes the controller class. Note PSX tries to 
autoload the given class name so the namespace must be therefor defined in 
the :file:`composer.json` autoload key. Here an example route entry:

.. code::

    GET|POST|PUT|DELETE /foo/bar Acme\Api\News\Endpoint

Webserver
---------

If you dont have a local webserver you can use the build in HTTP server of PHP.
You can start the server with the following command:

.. code::

    php -S 127.0.0.1:8000 public/index.php

The configuration file should have the following entries:

.. code::

    'psx_url'                 => 'http://127.0.0.1:8000',
    'psx_dispatch'            => '',
