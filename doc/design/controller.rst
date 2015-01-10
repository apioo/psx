
Controller
==========

Abstract
--------

This chapter gives a short overview of the most important methods which you need
inside an controller. To simplify things take a look at the following source 
code

Usage
-----

.. code-block:: php

    <?php
    
    use PSX\ControllerAbstract;

    class Controller extends ControllerAbstract;
    {
        /**
         * @Inject
         * @var PSX\TemplateInterface
         */
        protected $template;

        public function doIndex()
        {
            // returns the request url as PSX\Uri
            $this->getUrl();

            // returns an request header
            $this->getHeader('Content-Type');

            // returns an GET parameter
            $this->getParameter('id');

            // returns the request body. For x-www-form-urlencoded or json data
            // this will be an array for xml an DOMDocument
            $body = $this->getBody();

            // returns the value for the key "title". This works with 
            // x-www-form-urlencoded, json or xml data
            $title = $this->getAccessor()->get('title');

            // if you want assign an value to the template which is not exposed
            // to the public you can assign it directly to the template
            $this->template->assign('foo', 'bar');

            // imports data from the request body into the record. See the 
            // data chapter for more informations
            $record = $this->import(new Record('foo', array('field1' => null)));;

            // set data to the response body. How this data is presented depends
            // on the Accept header or GET parameter "format"
            $this->setBody(array(
                'foo' => 'bar'
            ));
        }
    }

Generation
----------

It is possible to generate an controller template. You can use the following 
command which takes as first argument the class name and as second a comma 
seperated list with service names. These services are automatically included in
the controller

.. code::

    $ ./vendor/bin/psx generate:controller Acme\Controller connection,http

