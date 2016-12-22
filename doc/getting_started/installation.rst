
Installation
============

PSX uses composer as dependency manager. In order to install PSX composer must
be installed on your system. More information how to install composer at 
https://getcomposer.org/.

The easiest way to start is to install the PSX sample project through composer:

.. code::

    $ php composer.phar create-project psx/sample .

This sample project contains a sample API and all classes which you need to
start with PSX.

In case you cant install composer PSX has also a pre-packaged release which
already includes all dependencies which you can download at
https://github.com/apioo/psx/releases

Configuration
-------------

The main configuration is defined in the file :file:`configuration.php` 
which is a simple php array with key value pairs. You must change the key 
"psx_url" so that it points to the psx public root. All other entries are 
optional.

.. literalinclude:: ../../configuration.php
   :language: php

If your application needs database access you can enter the credentials in the
:code:`psx_connection` key. The connection service provides a Doctrine DBAL 
connection which you can use in your application.

Routing
-------

In order to make a controller accessible you have to define a route in your 
:file:`routes` file. If a request arrives at an endpoint PSX tries to autoload 
the provided class through composer. Here an example route entry:

.. code::

    GET|POST|PUT|DELETE /foo/bar Acme\Api\News\Endpoint

This would invoke the class `Acme\\Api\\News\\Endpoint` if you visit the route
`/foo/bar`. All controller classes must extend the class
`PSX\\Framework\\Controller\\ControllerAbstract`

Webserver
---------

If you dont have a local web server you can use the build in HTTP server of PHP.
You can start the server with the following command:

.. code::

    php -S 127.0.0.1:8000 public/index.php

The configuration file should have then the following entries:

.. code::

    'psx_url'                 => 'http://127.0.0.1:8000',
    'psx_dispatch'            => '',
