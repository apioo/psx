
Request lifecycle
=================

This chapter explains the request lifecycle of an PSX application which helps
you to better understand how PSX works.

Http request/response
---------------------

PSX uses an standard HTTP request and response interface. At the start of the 
application lifecycle an HTTP request and response object will be created.

.. literalinclude:: ../../library/PSX/Dispatch/RequestFactoryInterface.php
   :language: php
   :lines: 30-38
   :prepend: <?php

.. literalinclude:: ../../library/PSX/Dispatch/ResponseFactoryInterface.php
   :language: php
   :lines: 30-38
   :prepend: <?php

After the request and response objects are created the loader searches the 
fitting controller based on the routing file. The controller must implement the
ApplicationStackInterface.

.. literalinclude:: ../../library/PSX/ApplicationStackInterface.php
   :language: php
   :lines: 30-40
   :prepend: <?php

Then the loader receives the application stack from the controller which is an
array containing callable or FilterInterface. Each middleware can then read from 
the request and write data to the response.

.. literalinclude:: ../../library/PSX/Dispatch/FilterInterface.php
   :language: php
   :lines: 33-42
   :prepend: <?php

After the stack was executed the response must be send to the client. This is 
done through an sender class which sends the header through the "header" 
function and outputs the response body via "echo".

.. literalinclude:: ../../library/PSX/Dispatch/SenderInterface.php
   :language: php
   :lines: 32-40
   :prepend: <?php

Events
------

Through the request lifecycle there are some places where PSX triggers an event.
The dependency event trait contains the default listeners. You can overload the 
:code:`appendDefaultListener` method to add new listeners. In the following a 
list of events with a short description.

+-----------------------------+-------------------------------------------------------+
| Event name                  | Description                                           |
+-----------------------------+-------------------------------------------------------+
| Event::REQUEST_INCOMING     | Triggered when an request arrives                     |
+-----------------------------+-------------------------------------------------------+
| Event::ROUTE_MATCHED        | Triggered when an route was found for the request     |
+-----------------------------+-------------------------------------------------------+
| Event::CONTROLLER_EXECUTE   | Triggered before an controller gets executed          |
+-----------------------------+-------------------------------------------------------+
| Event::CONTROLLER_PROCESSED | Triggered after an controller was executed            |
+-----------------------------+-------------------------------------------------------+
| Event::RESPONSE_SEND        | Triggered before the response gets send to the client |
+-----------------------------+-------------------------------------------------------+
| Event::EXCEPTION_THROWN     | Triggered when an exception occurs                    |
+-----------------------------+-------------------------------------------------------+
| Event::COMMAND_EXECUTE      | Triggered before an command gets executed             |
+-----------------------------+-------------------------------------------------------+
| Event::COMMAND_PROCESSED    | Triggered after an command was executed               |
+-----------------------------+-------------------------------------------------------+

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
In adition you could also overwrite the :code:`getPreFilter` or 
:code:`getPostFilter` method which are merged together to the application 
stack i.e.:

.. literalinclude:: ../../library/PSX/ControllerAbstract.php
   :language: php
   :lines: 112-119
   :prepend: <?php

