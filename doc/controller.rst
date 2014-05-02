
Controller
==========

This chapter gives a short overview of the most important methods which you need
inside an controller. To simplify things take a look at the following source 
code

.. code-block:: php

    <?php
    
    class Controller extends \PSX\Controller\ViewAbstract
    {
        public function doIndex()
        {
        	// returns the request url as PSX\Url
        	$this->getUrl();

        	// returns an request header
        	$this->getHeader('Content-Type');

        	// returns an GET parameter
        	$this->getParameter('id');

        	// returns the request body as an array. This works with normal 
        	// x-www-form-urlencoded form submits, json and xml data
        	$body = $this->getBody();

        	// the body gets imported into an DOMDocument
        	$body = $this->getBody(ReaderInterface::DOM);


        	// inside the controller you can access every service from the DI
        	// container
        	$this->getEntityManager();

        	// if you want assign an value to the template which is not exposed
        	// to the public you can assign it directly to the template
        	$this->getTemplate()->assign('foo', 'bar');

            // imports data from the request body into the record. See the 
            // chapter import data for more informations
            $record = $this->import(new Record('foo', array('field1' => null)));;


        	// set data to the response body. How this data is presented depends
        	// on the Accept header or GET parameter "format"
        	$this->setBody(array(
        		'foo' => 'bar'
        	));
        }
    }
