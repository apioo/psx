
Request lifecycle
=================

Http request/response
---------------------

PSX uses an standard HTTP request and response interface. At the start of the 
application lifecycle an HTTP request and response object will be created.

.. literalinclude:: ../../library/PSX/Dispatch/RequestFactoryInterface.php
   :language: php
   :lines: 33-42
   :prepend: <?php

.. literalinclude:: ../../library/PSX/Dispatch/ResponseFactoryInterface.php
   :language: php
   :lines: 33-42
   :prepend: <?php

After the request and response objects are created the loader searches the 
fitting controller based on the routing file. The controller must implement the
ApplicationStackInterface.

.. literalinclude:: ../../library/PSX/ApplicationStackInterface.php
   :language: php
   :lines: 33-43
   :prepend: <?php

Then the loader receives the application stack from the controller which is an
array containing callable or FilterInterface. Each middleware can then read from 
the request and write data to the response.

.. literalinclude:: ../../library/PSX/Dispatch/FilterInterface.php
   :language: php
   :lines: 36-45
   :prepend: <?php

After the stack was executed the response must be send to the client. This is 
done through an sender class which sends the header through the "header" 
function and outputs the response body via "echo".

.. literalinclude:: ../../library/PSX/Dispatch/SenderInterface.php
   :language: php
   :lines: 35-43
   :prepend: <?php

Middleware
----------

In PSX an middleware must be either an FilterInterface or callable i.e. the most 
basic "hello world" example would be:

.. code-block:: php

    <?php

    use PSX\ControllerAbstract;

    class Controller extends ControllerAbstract
    {
        public function getApplicationStack()
        {
            return [function($request, $response){
                $response->getBody()->write('Hello world');
            }];
        }
    }

By default the controller returns the ControllerExecutor middleware which simply
calls the on* methods and optional the method which was set in the routes file.
In adition you could also overwrite the getPreFilter or getPostFilter method
which are merged together to the application stack i.e.:

.. literalinclude:: ../../library/PSX/ControllerAbstract.php
   :language: php
   :lines: 94-103
   :prepend: <?php

