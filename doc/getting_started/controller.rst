
Controller
==========

To build an API endpoint we need to tell PSX which request methods are
available and how the incoming and outgoing data looks. Because of this PSX 
can automatically validate incoming data and format outgoing data according to 
the schema. Also it is possible to generate documentation or other schema 
formats like i.e. Swagger, WSDL or RAML based on the defined schema. In the 
following we describe the available options.

Annotation
----------

It is possible to provide all API informations to the controller through 
annotations.

.. code-block:: php

    <?php

    namespace PSX\Project;

    use PSX\Framework\Controller\SchemaApiAbstract;
    use PSX\Record\RecordInterface;

    /**
     * @Title("Endpoint")
     * @PathParam(name="foo_id", type="integer")
     */
    class Endpoint extends SchemaApiAbstract
    {
        /**
         * @QueryParam(name="count", description="Count of comments")
         * @Outgoing(code=200, schema="PSX\Project\Model\Song")
         */
        protected function doGet()
        {
            $count = $this->queryParameters->getProperty('count');

            // @TODO return the result i.e. from a database

            return new Song('Wut ueber den verlorenen groschen', 'Beethoven');
        }

        /**
         * @Incoming(schema="PSX\Project\Model\Song")
         * @Outgoing(code=201, schema="PSX\Project\Model\Message")
         */
        protected function doPost($record)
        {
            // @TODO work with the record

            return new Message(true, 'Successful created');
        }
    }

The schema must be either a simple POPO class which contains annotations to 
describe the schema or a json schema file. The model class 
`PSX\\Project\\Model\\Song` could look like:

.. code-block:: php

    class Song
    {
        /**
         * @Type("string")
         */
        protected $title;

        /**
         * @Type("string")
         */
        protected $artist;
        
        public function __construct($title = null, $artist = null)
        {
            $this->title  = $title;
            $this->artist = $artist;
        }

        public function getTitle()
        {
            return $this->title;
        }

        public function setTitle($title)
        {
            $this->title = $title;
        }

        public function getArtist()
        {
            return $this->artist;
        }

        public function setArtist($artist)
        {
            $this->artist = $artist;
        }
    }

More informations at the psx schema project. The following annotations are 
available for the controller:

+--------------+--------------+---------------------------------------------------------+
| Annotation   | Target       | Example                                                 |
+==============+==============+=========================================================+
| @Description | Class/Method | @Description("Bar")                                     |
+--------------+--------------+---------------------------------------------------------+
| @Exclude     | Method       | @Exclude                                                |
+--------------+--------------+---------------------------------------------------------+
| @Incoming    | Method       | @Incoming(schema="PSX\\Project\\Model\\Song")           |
+--------------+--------------+---------------------------------------------------------+
| @Outgoing    | Method       | @Outgoing(code=200, schema="PSX\\Project\\Model\\Song") |
+--------------+--------------+---------------------------------------------------------+
| @PathParam   | Class        | @PathParam(name="foo", type="integer")                  |
+--------------+--------------+---------------------------------------------------------+
| @QueryParam  | Method       | @QueryParam(name="bar", type="integer")                 |
+--------------+--------------+---------------------------------------------------------+
| @Title       | Class/Method | @Title("Foo")                                           |
+--------------+--------------+---------------------------------------------------------+

RAML
----

Instead of annotations it is also possible to provide a schema file which 
describes the endpoint. At the moment we support the RAML (http://raml.org/) 
specification.

.. code-block:: php

    <?php

    namespace PSX\Project;

    use PSX\Api\Parser\Raml;
    use PSX\Framework\Controller\SchemaApiAbstract;
    use PSX\Framework\Loader\Context;
    use PSX\Record\RecordInterface;

    class Endpoint extends SchemaApiAbstract
    {
        public function getDocumentation($version = null)
        {
            return Raml::fromFile(__DIR__ . '/endpoint.raml', $this->context->get(Context::KEY_PATH));
        }

        protected function doGet()
        {
            $count = $this->queryParameters->getProperty('count');

            // @TODO return the result i.e. from a database

            return [
                'title'  => 'Wut ueber den verlorenen groschen',
                'artist' => 'Beethoven',
            ];
        }

        protected function doPost(RecordInterface $record)
        {
            // @TODO work with the record

            return [
                'success' => true,
                'message' => 'Successful created',
            ];
        }
    }

RAML definition (endpoint.raml)

.. code-block:: yaml

    #%RAML 0.8
    title: Endpoint
    baseUri: http://example.phpsx.org
    /endpoint/{foo_id}:
      uriParameters:
        foo_id:
          type: integer
      get:
        queryParameters:
          count:
            type: integer
        responses:
          200:
            body:
              application/json:
                schema: !include schema/song.json
      post:
        body:
          application/json:
            schema: !include schema/song.json
        responses:
          201:
            body:
              application/json:
                schema: !include schema/message.json
