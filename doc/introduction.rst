
Introduction
============

About
-----

PSX is a framework written in PHP to create RESTful APIs. It helps you building 
clean URLs serving web standard formats like JSON, XML, Atom and RSS. At the 
core PSX is build of three parts. A handler system (similar to repositories in 
doctrine) wich abstracts away the actual SQL queries from the domain logic. An 
routing system wich executes the fitting controller method depending on	the 
location of the controller and the annotation of the method. And an flexible 
data system to convert data records from the database into different formats 
like JSON, XML, Atom and RSS. PSX uses a lightweight DI container to handle 
dependencies (but is also compatible with the symfony DI container). The 
controller can return request or response filter wich can react or modify the 
HTTP request or response. PSX offers some basic request filter to handle i.e. 
Basic or Oauth authentication. In addition PSX offers some cool components to 
use and implement OAuth, OpenID, Opengraph, Opensocial, Opensearch, 
PubSubHubbub, WebFinger, Atom, and RSS. At the example page you can see sample 
implementations using various PSX classes.

Installation
------------

If you want use PSX fully including the routing mechanism you have to download 
the framework manually because the requests must go to the index.php. If you 
only want use specific classes of PSX you can install it via Composer.

Manual
^^^^^^

You can download the current version of psx from the offical website phpsx.org. 
Put the folder on your web or local server. Goto http://host/path/to/psx/public 
and if you see a website with the title "Template sample" psx is running 
successfully.

Composer
^^^^^^^^

If you use classes of PSX in your application you can add PSX as dependency in 
your composer.json file. The package name is psx/psx. More informations at 
packagist.org.

Configuration
-------------

The config is an php array with key value pairs. You must change the key 
"psx_url" so that it points to the psx public root. All other entries are 
optional. The following table describes each entry

.. code-block:: php

    <?php

    $config = array(
    
    	// The url to the psx public folder (i.e. http://127.0.0.1/psx/public)
    	'psx_url'                 => 'http://127.0.0.1/projects/psx/public',

    	// Where we get the input path normally 'index.php/' or '' if you use 
    	// mod_rewrite
    	'psx_dispatch'            => 'index.php/',

    	// The default timezone
    	'psx_timezone'            => 'UTC',

    	// Whether to gzip the output of psx. The content gets only compressed 
    	// if the browser support gzip
    	'psx_gzip'                => false,

    	// Whether PSX runs in debug mode or not. If not the error reporting is 
    	// set to 0.
    	'psx_debug'               => true,
    
    	// Your SQL connections
    	'psx_sql_host'            => 'localhost',
    	'psx_sql_user'            => 'root',
    	'psx_sql_pw'              => '',
    	'psx_sql_db'              => 'psx',
    
    	// Path to the routing file
    	'psx_routing'             => '../routes',

    	// Path to a custom error template file
    	'psx_error_template'      => null,

    	// Path to the cache folder
    	'psx_path_cache'          => '../cache',

    	// Path to the library folder
    	'psx_path_library'        => '../library',
    
    );

Help
----

Because PSX is in an early stage the manual is not complete. I appreciate every 
help in making this documentation better. The documentation is writte in 
reStructuredText and uses the Sphinx documentation generator. You can checkout 
the current version of this manual via GIT. If you have made some changes that 
you want commit please contact me.
