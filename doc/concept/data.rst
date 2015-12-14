
Data
====

Abstract
--------

This chapter should give a overview how data handling works in PSX. That means
how you can read data from a request and write a response

Reading requests
----------------

If we want import data from a request we have to known the format of the 
request body. PSX uses the content type header to determine which reader should 
be used. I.e. if we have an application/xml content type we use the xml reader 
and if we have an application/json content type we use the json reader. The 
reader returns the request data in a form which can be easily used in php. I.e. 
the xml reader returns a DOMDocument and the json reader returns a stdClass.

.. literalinclude:: ../../library/PSX/Data/ReaderInterface.php
   :language: php
   :lines: 33-55
   :prepend: <?php

Since we need a uniform structure of the data we must apply a transformation in
some cases. The transformation depends also on the content type. If we receive
an application/xml content type the XmlArray transformer gets applied.

.. literalinclude:: ../../library/PSX/Data/TransformerInterface.php
   :language: php
   :lines: 33-49
   :prepend: <?php

Then it is possible to import the data into a record through a importer class. 
In abstract a importer class takes meta informations from a source and returns 
a record class containing the data.

.. literalinclude:: ../../library/PSX/Data/Record/ImporterInterface.php
   :language: php
   :lines: 31-52
   :prepend: <?php

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

Importer
--------

Which importer gets used depends on the source. You can pass different objects 
to the import method which in the end provides meta informations how the 
incoming request data looks. If you pass to the source a 
PSX\\Data\\RecordInterface the annotations of the record class gets parsed. I.e. 
if you want import an atom xml format you could use the following controller

.. code-block:: php

    <?php

    namespace Foo\Application;

    use PSX\Atom;
    use PSX\ControllerAbstract;

    class Index extends ControllerAbstract
    {
        public function doIndex()
        {
            $atom = $this->import(new Atom());

            // do something with the atom record i.e. $atom->getTitle();
        }
    }

The content type application/atom+xml has also a transformer registered which
builds a data structure from the DOMDocument which then gets imported into the 
Atom record.

It is also possible to pass a schema definition to the import method. The data
will be validated against this schema. This has also the advantage that you can 
use the schema to generate great documentation about the API

.. code-block:: php

    <?php

    namespace Foo\Application;

    use PSX\ControllerAbstract;

    class Index extends ControllerAbstract
    {
        /**
         * @Inject
         * @var PSX\Data\Schema\SchemaManager
         */
        protected $schemaManager;

        public function doIndex()
        {
            $entry = $this->import($this->schemaManager->getSchema('Foo\Schema\Entry'));

            // do something with the entry
        }
    }

Here an example schema from a test case

.. literalinclude:: ../../tests/PSX/Controller/Foo/Schema/Entry.php
   :language: php
   :lines: 32-47
   :prepend: <?php

More detailed informations about the process at :doc:`import_data`

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

Serializer
----------

PSX integrates the JMS serializer library to serialize arbitrary objects. This
is useful if you use i.e. a ORM like doctrine where you have objects which
contain fields which you want expose per API. Here an example how you could use 
the serializer

.. code-block:: php

    <?php

    class FooController extends ControllerAbstract
    {
        /**
         * @Inject
         * @var PSX\Data\SerializerInterface
         */
        protected $serializer;

        /**
         * @Inject
         * @var Doctrine\ORM\EntityManager
         */
        protected $entityManager;

        public function doIndex()
        {
            $news = $this->entityManager->getRepository('Foo\BarRepository')->findAll();

            $this->setBody(array(
                'news' => $this->serialize($news),
            ));
        }
    }

More informations how the serializer works at https://github.com/schmittjoh/serializer.
