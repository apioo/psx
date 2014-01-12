
Routing
=======

Routing is the process to delegate the request from the user to the fitting 
controller. By default PSX uses a simple routing file wich contains all 
available routes. The routing file was inspired by the java play framework

Routing file
------------

PSX parses the routing file and the route which matches the most is taken. Lets
see the following example routing file

.. code-block:: none

    # example routing file

    GET|POST / Foo\News\Application\Index
    GET /api Foo\News\Application\Api

If we make an GET request to /api/foo the Foo\\News\\Application\\Api controller 
gets loaded. If we make an POST request the Foo\\News\\Application\\Index
controller gets loaded since the /api route doesnt support POST requests

Controller annotation
---------------------

If a fitting controller was found the loader of PSX will call the method 
depending on the remaining path. The remaining path is the request path without
the path from the route. In our example the remaining path is /foo. Lets assume 
we have the following controller

.. code-block:: php

    <?php
    
    namespace Foo\News\Application;
    
    use PSX\ModuleAbstract;
    
    class Api extends ModuleAbstract
    {
        /**
         * @httpMethod GET
         * @path /
         */
        public function doIndex()
        {
        }
    
        /**
         * @httpMethod POST
         * @path /insert
         */
        public function doInsert()
        {
        }
    }

In this case the method doIndex would be called since we have found no other 
method which has an @Path /foo annotation.

Custom location finder
----------------------

How the location of the class is obtained depends on the location finder. You 
can give the PSX loader a custom location finder. The 
PSX\\Loader\\LocationFinderInterface has the following interface

.. literalinclude:: ../library/PSX/Loader/LocationFinderInterface.php
   :language: php
   :lines: 33-42
   :prepend: <?php

The location finder must return an location object wich contains an id for this 
location, the remaining path and the ReflectionClass from the controller. The 
loader will then call the fitting method of the controller depending on the 
annotation. In this way you can simply write your own location finder wich gets 
the information from an database or something else.
