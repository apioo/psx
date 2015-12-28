
Schema
======

To build a documented API we need to tell PSX which request methods are 
available and how the incoming and outgoing data looks. Because of this PSX 
can automatically validate incoming data and format outgoing data according to 
the schema. Also it is possible to generate documentation or other schema 
formats like i.e. Swagger, WSDL or RAML based on the defined schema.

Definition
----------

The easiest way to provide these informations for our API endpoint is to use
a RAML file. RAML (http://raml.org/) is a general specification to describe an
API endpoint. In the following a sample API with the fitting RAML specification. 
You can copy this example and play with the API to test the behaviour.

.. code-block:: php

    <?php

    namespace Foo;

    use PSX\Api\Documentation\Parser\Raml;
    use PSX\Api\Version;
    use PSX\Controller\SchemaApiAbstract;
    use PSX\Data\RecordInterface;
    use PSX\Loader\Context;

    class Endpoint extends SchemaApiAbstract
    {
        public function getDocumentation()
        {
            return Raml::fromFile(__DIR__ . '/endpoint.raml', $this->context->get(Context::KEY_PATH));
        }

        protected function doGet(Version $version)
        {
            $count = $this->queryParameters->getProperty('count');

            // @TODO return the result i.e. from an database

            return [
                'title'  => 'Wut ueber den verlorenen groschen',
                'artist' => 'Beethoven',
            ];
        }

        protected function doPost(RecordInterface $record, Version $version)
        {
            // @TODO work with the record

            return [
                'success' => true,
                'message' => 'Successful created',
            ];
        }

        protected function doPut(RecordInterface $record, Version $version)
        {
            // @TODO work with the record

            return [
                'success' => true,
                'message' => 'Successful updated',
            ];
        }

        protected function doDelete(RecordInterface $record, Version $version)
        {
            // @TODO work with the record

            return [
                'success' => true,
                'message' => 'Successful deleted',
            ];
        }
    }

RAML definition (endpoint.raml)

.. code-block:: yaml

    #%RAML 0.8
    title: PSX test API
    version: v1
    /endpoint:
      get:
        queryParameters:
          count:
            description: filter the songs by genre
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
          200:
            body:
              application/json:
                schema: !include schema/message.json

JSON schema (song.json)

.. code-block:: json

    {
        "$schema": "http://json-schema.org/draft-04/schema#",
        "description": "A canonical song",
        "type": "object",
        "properties": {
            "artist": {
                "type": "string"
            },
            "title": {
                "type": "string"
            }
        }
    }

JSON schema (message.json)

.. code-block:: json

    {
        "$schema": "http://json-schema.org/draft-04/schema#",
        "description": "A status message",
        "type": "object",
        "properties": {
            "message": {
                "type": "string"
            },
            "success": {
                "type": "boolean"
            }
        }
    }

Internally the RAML parser creates resource objects which you can also produce 
manually. In the following a schema API which defines the resources in PHP.

.. code-block:: php

    <?php

    namespace Acme\Api\News;

    use PSX\Api\Documentation;
    use PSX\Api\Resource;
    use PSX\Api\Version;
    use PSX\Controller\SchemaApiAbstract;
    use PSX\Data\RecordInterface;
    use PSX\Data\Schema\Property;
    use PSX\Loader\Context;

    class Endpoint extends SchemaApiAbstract
    {
        /**
         * @Inject
         * @var PSX\Data\Schema\SchemaManager
         */
        protected $schemaManager;

        public function getDocumentation()
        {
            $resource = new Resource(Resource::STATUS_ACTIVE, $this->context->get(Context::KEY_PATH));

            $resource->addMethod(Resource\Factory::getMethod('GET')
                ->addQueryParameter(Property::getInteger('count'))
                ->addResponse(200, $this->schemaManager->getSchema('Acme\Schema\Song')));

            $resource->addMethod(Resource\Factory::getMethod('POST')
                ->setRequest($this->schemaManager->getSchema('Acme\Schema\Song'))
                ->addResponse(200, $this->schemaManager->getSchema('Acme\Schema\Message')));

            return new Documentation\Simple($resource, 'Sample API');
        }

        // ..
    }

Here an example how to create a simple schema which can be used through the 
schema manager.

.. code-block:: php

    <?php

    namespace Acme\Schema;

    use PSX\Data\SchemaAbstract;

    class News extends SchemaAbstract
    {
        public function getDefinition()
        {
            $sb = $this->getSchemaBuilder('news');
            $sb->integer('userId');
            $sb->string('title')
                ->setPattern('[A-z]+');
            $sb->dateTime('created');

            return $sb->getProperty();
        }
    }

It is also possible to generate such a schema from a sql database. You can use 
the following command:

.. code::

    $ ./vendor/bin/psx generate:schema Acme\Schema\News news_table

