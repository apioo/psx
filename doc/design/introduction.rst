
Introduction
============

About
-----

PSX is a framework written in PHP to create RESTful APIs. It provides tools to 
handle routing, API versioning, data transformation, authentication, 
documentation and testing. With PSX you can easily build an REST API around an 
existing application or create a new one from scratch. More informations at
http://phpsx.org

Installation
------------

You have three options in order to install PSX. Either download a current 
release from github

    https://github.com/k42b3/psx/releases

or install the PSX sample project via composer

    php composer.phar create-project psx/sample .

or require PSX as dependency in your composer.json

    "psx/psx": "0.9.*@dev"

Configuration
-------------

The main configuration is defined in the configuration file :file:`configuration.php` 
which is an simple php array with key value pairs. You must change the key 
"psx_url" so that it points to the psx public root. All other entries are 
optional.

.. literalinclude:: ../../configuration.php
   :language: php

Help
----

Because PSX is in an early stage the manual is not complete. I appreciate every 
help in making this documentation better. The documentation is written in 
reStructuredText and uses the sphinx documentation generator. If you have made 
changes that you want commit please submit a pull request.
