
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

    class Controller extends ControllerAbstract
    {
        /**
         * @Inject
         * @var \PSX\Framework\Template\TemplateInterface
         */
        protected $template;

        public function doIndex()
        {
            // returns the request url as PSX\Uri\Uri
            $this->getUrl();

            // returns a request header
            $this->getHeader('Content-Type');

            // returns a GET parameter
            $this->getParameter('id');

            // returns the request body. For x-www-form-urlencoded or json data
            // this will be a stdClass for xml a DOMDocument
            $body = $this->getBody();

            // imports data from the request body into the model
            $model = $this->getBodyAs(Model::class);

            // set data to the response body. How this data is presented depends
            // on the Accept header or GET parameter "format"
            $this->setBody([
                'foo' => 'bar'
            ]);
        }
    }
