
Request lifecycle
=================

This chapter explains the request lifecycle of a PSX application.

Http request/response
---------------------

PSX uses a standard HTTP request and response interface. At the start of the 
application lifecycle a HTTP request and response object will be created.

.. code-block:: php

    interface RequestFactoryInterface
    {
        /**
         * Returns the http request containing all values from the environment
         *
         * @return \PSX\Http\RequestInterface
         */
        public function createRequest();
    }

.. code-block:: php

    interface ResponseFactoryInterface
    {
        /**
         * Returns the http response containing default values and the body stream
         *
         * @return \PSX\Http\ResponseInterface
         */
        public function createResponse();
    }

After the request and response objects are created the loader searches the 
fitting controller based on the routing file. The controller must implement the
ApplicationStackInterface.

.. code-block:: php

    interface ApplicationStackInterface
    {
        /**
         * Returns an array containing FilterInterface or callable. The request and
         * response object gets passed to each filter which then can produce the
         * response
         *
         * @return \PSX\Framework\Filter\FilterInterface[]|\Closure[]
         */
        public function getApplicationStack();
    }

Then the loader receives the application stack from the controller which is an
array containing callable or FilterInterface. Each middleware can then read from 
the request and write data to the response.

.. code-block:: php

    interface FilterInterface
    {
        /**
         * @param \PSX\Http\RequestInterface $request
         * @param \PSX\Http\ResponseInterface $response
         * @param \PSX\Framework\Filter\FilterChainInterface $filterChain
         * @return void
         */
        public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain);
    }

After the stack was executed the response must be send to the client. This is 
done through a sender class which sends the header through the "header" 
function and outputs the response body via "echo".

.. code-block:: php

    interface SenderInterface
    {
        /**
         * Method to send the response which was created to the browser
         *
         * @param \PSX\Http\ResponseInterface $response
         */
        public function send(ResponseInterface $response);
    }

Events
------

Through the request lifecycle there are some places where PSX triggers an event.
In the following a list of events with a short description.

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

In PSX a middleware must be either a FilterInterface or callable i.e. the most 
basic "hello world" example would be:

.. code-block:: php

    <?php

    use PSX\Framework\Controller\ControllerAbstract;

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
stack.
