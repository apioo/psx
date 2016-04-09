
Data
====

Abstract
--------

This chapter should give an overview how data handling works in PSX. That means
how you can read data from a request and write a response

Reading requests
----------------

If we want import data from a request we have to known the format of the 
request body. PSX uses the content type header to determine which reader should 
be used. I.e. if we have an `application/xml` content type we use the xml reader 
and if we have an `application/json` content type we use the json reader. The 
reader returns the request data in a form which can be easily used in php. I.e. 
the xml reader returns a DOMDocument and the json reader returns a stdClass.

.. code-block:: php

    interface ReaderInterface
    {
        /**
         * Transforms the $request into an parseable form this can be an array
         * or DOMDocument etc.
         *
         * @param string $data
         * @return mixed
         */
        public function read($data);

        /**
         * Returns whether the content type is supported by this reader
         *
         * @param \PSX\Http\MediaType $contentType
         * @return boolean
         */
        public function isContentTypeSupported(MediaType $contentType);
    }

Since we need a uniform structure of the data we must apply a transformation in
some cases. The transformation depends also on the content type. If we receive
an `application/xml` content type the XmlArray transformer gets applied.

.. code-block:: php

    interface TransformerInterface
    {
        /**
         * Transforms the data into a readable state
         *
         * @param string $data
         * @return array
         */
        public function transform($data);
    }

Available data reader
---------------------

PSX comes with the following data reader:

+--------------------------+-----------------------------------+-------------+
| Class                    | Content-Type                      | Return-Type |
+==========================+===================================+=============+
| PSX\\Data\\Reader\\Form  | application/x-www-form-urlencoded | stdClass    |
+--------------------------+-----------------------------------+-------------+
| PSX\\Data\\Reader\\Json  | application/json                  | stdClass    |
+--------------------------+-----------------------------------+-------------+
| PSX\\Data\\Reader\\Xml   | application/xml                   | DOMDocument |
+--------------------------+-----------------------------------+-------------+

The result of the data reader can be obtained by using the getBody method inside
the controller. Depending on the content type the response is either a stdClass
or DOMDocument.

.. code-block:: php

    <?php

    namespace Foo\Application;

    use PSX\ControllerAbstract;

    class Index extends ControllerAbstract
    {
        public function doIndex()
        {
            $body = $this->getBody();

            // @TODO do something with the body
        }
    }

Writing responses
-----------------

PSX analyzes the object graph which was produced by the controller and uses a
writer to produce the fitting data format for the client. What content is served 
depends on the Accept header or the GET parameter format. More informations 
about the object graph at :doc:`object_graph`

Available data writer
---------------------

In the following an overview of available writer in PSX: 

+--------------------------+------------------------+------------------+
| Class                    | Content-Type           | Format-Parameter |
+==========================+========================+==================+
| PSX\\Data\\Writer\\Html  | text/html              | html             |
+--------------------------+------------------------+------------------+
| PSX\\Data\\Writer\\Json  | application/json       | json             |
+--------------------------+------------------------+------------------+
| PSX\\Data\\Writer\\Jsonp | application/javascript | jsonp            |
+--------------------------+------------------------+------------------+
| PSX\\Data\\Writer\\Jsonx | application/jsonx+xml  | jsonx            |
+--------------------------+------------------------+------------------+
| PSX\\Data\\Writer\\Atom  | application/atom+xml   | atom             |
+--------------------------+------------------------+------------------+
| PSX\\Data\\Writer\\Soap  | application/soap+xml   | soap             |
+--------------------------+------------------------+------------------+
| PSX\\Data\\Writer\\Xml   | application/xml        | xml              |
+--------------------------+------------------------+------------------+

Use case
--------

Lets take a look at the following controller.

.. code-block:: php

    <?php

    class FooController extends ControllerAbstract
    {
        public function doIndex()
        {
            $atom = new Atom();
            $atom->setTitle('lorem ipsum');

            $this->setBody($atom);
        }
    }

If you would request this method with a normal browser PSX would try to display
the data as HTML (since most browsers send an Accept header with text/html). 
Therefor it would use the html writer which assigns the data to the template. In 
your template you can then build the html representation of the feed. If we 
would make the request containing an Accept header application/json or GET 
parameter "format" containing "json" the data would be returned as json format. 
If we would provide an application/atom+xml the atom feed gets returned.

