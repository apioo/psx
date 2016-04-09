
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

    use PSX\Framework\Controller\AnnotationApiAbstract;
    use PSX\Record\RecordInterface;

    /**
     * @Title("Endpoint")
     * @PathParam(name="foo_id", type="integer")
     */
    class Endpoint extends AnnotationApiAbstract
    {
        /**
         * @QueryParam(name="count", description="Count of comments")
         * @Outgoing(code=200, schema="schema/song.json")
         */
        protected function doGet()
        {
            $count = $this->queryParameters->getProperty('count');

            // @TODO return the result i.e. from a database

            return [
                'title'  => 'Wut ueber den verlorenen groschen',
                'artist' => 'Beethoven',
            ];
        }

        /**
         * @Incoming(schema="schema/song.json")
         * @Outgoing(code=201, schema="schema/message.json")
         */
        protected function doPost(RecordInterface $record)
        {
            // @TODO work with the record

            return [
                'success' => true,
                'message' => 'Successful created',
            ];
        }
    }

The file `schema/song.json` contains the json schema of the data and could look
like:

.. code-block:: json

    {
      "$schema": "http://json-schema.org/draft-04/schema#",
      "title": "song",
      "type": "object",
      "properties": {
        "title": {
          "type": "string"
        },
        "artist": {
          "type": "string"
        }
      }
    }

The following annotations are available:

+--------------+--------------+------------------------------------------------+
| Annotation   | Target       | Example                                        |
+==============+==============+================================================+
| @Description | Class/Method | @Description("Bar")                            |
+--------------+--------------+------------------------------------------------+
| @Exclude     | Method       | @Exclude                                       |
+--------------+--------------+------------------------------------------------+
| @Incoming    | Method       | @Incoming(schema="schema/song.json")           |
+--------------+--------------+------------------------------------------------+
| @Outgoing    | Method       | @Outgoing(code=200, schema="schema/song.json") |
+--------------+--------------+------------------------------------------------+
| @PathParam   | Class        | @PathParam(name="foo", type="integer")         |
+--------------+--------------+------------------------------------------------+
| @QueryParam  | Method       | @QueryParam(name="bar", type="integer")        |
+--------------+--------------+------------------------------------------------+
| @Title       | Class/Method | @Title("Foo")                                  |
+--------------+--------------+------------------------------------------------+

RAML
----

It is possible to use an existing RAML (http://raml.org/) specification to build 
the API endpoint. In the following a sample API with the fitting RAML 
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
