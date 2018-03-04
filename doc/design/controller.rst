
Controller
==========

Abstract
--------

This chapter gives a short overview of the most important methods which you can 
use inside a controller.

Usage
-----

.. code-block:: php

    <?php
    
    namespace Foo\Bar;

    use PSX\Framework\Controller\ControllerAbstract;
    use PSX\Http\RequestInterface;
    use PSX\Http\ResponseInterface;

    class Controller extends ControllerAbstract
    {
        public function onGet(RequestInterface $request, ResponseInterface $response)
        {
            // returns the request url as PSX\Uri\Uri
            $request->getUri();

            // returns a request header
            $request->getHeader('Content-Type');

            // returns a GET parameter
            $request->getUri()->getParameter('id');

            // returns the request body
            $body = $this->requestReader->getBody($request);

            // imports data from the request body into the model
            $model = $this->requestReader->getBodyAs($request, Model::class);

            // set data to the response body
            $this->responseWriter->setBody($response, [
                'foo' => 'bar'
            ]);
        }
    }
