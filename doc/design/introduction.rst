
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

install the PSX sample project via composer

    php composer.phar create-project psx/sample .

or require PSX as dependency in your composer.json

    "psx/psx": "0.9.*@dev"

Configuration
-------------

The configuration file is an simple php array with key value pairs. You must 
change the key "psx_url" so that it points to the psx public root. All other 
entries are optional. The following code describes each entry

.. code-block:: php

    <?php

    return array(

        // The url to the psx public folder (i.e. http://127.0.0.1/psx/public)
        'psx_url'                 => 'http://127.0.0.1/projects/psx/public',

        // The input path 'index.php/' or '' if you use mod_rewrite
        'psx_dispatch'            => 'index.php/',

        // The default timezone
        'psx_timezone'            => 'UTC',

        // Whether PSX runs in debug mode or not. If not the error reporting is 
        // set to 0
        'psx_debug'               => true,

        // Your SQL connections
        'psx_sql_host'            => 'localhost',
        'psx_sql_user'            => 'root',
        'psx_sql_pw'              => '',
        'psx_sql_db'              => 'psx',

        // Path to the routing file
        'psx_routing'             => __DIR__ . '/routes',

        // Path to the cache folder
        'psx_path_cache'          => __DIR__ . '/cache',

        // Path to the library folder
        'psx_path_library'        => __DIR__ . '/library',

        // Class name of the error controller
        //'psx_error_controller'    => null,

        // If you only want to change the appearance of the error page you can 
        // specify a custom template
        //'psx_error_template'      => null,

    );

Help
----

Because PSX is in an early stage the manual is not complete. I appreciate every 
help in making this documentation better. The documentation is written in 
reStructuredText and uses the sphinx documentation generator. If you have made 
changes that you want commit please submit a pull request.
