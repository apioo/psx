
Schema
======

To build a documented API we need to tell PSX which request methods are 
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

    namespace Foo;

    use PSX\Api\Version;
    use PSX\Controller\AnnotationApiAbstract;
    use PSX\Data\RecordInterface;

    /**
     * @Title("Endpoint")
     * @PathParam(name="foo_id", type="integer")
     */
    class Endpoint extends AnnotationApiAbstract
    {
        /**
         * @QueryParam(name="count", description="filter the songs by genre")
         * @Outgoing(code=200, schema="schema/song.json")
         */
        protected function doGet(Version $version)
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
        protected function doPost(RecordInterface $record, Version $version)
        {
            // @TODO work with the record

            return [
                'success' => true,
                'message' => 'Successful created',
            ];
        }
    }

The following annotations are available:

+--------------+--------------+-----------------------------------------------------------------+
| Annotation   | Target       | Example                                                         |
+==============+==============+=================================================================+
| @Title       | Class/Method | @Title("Foo")                                                   |
+--------------+--------------+-----------------------------------------------------------------+
| @Description | Class/Method | @Description("Bar")                                             |
+--------------+--------------+-----------------------------------------------------------------+
| @PathParam   | Class        | @PathParam(name="foo", type="integer")                          |
+--------------+--------------+-----------------------------------------------------------------+
| @QueryParam  | Method       | @QueryParam(name="bar", type="integer")                         |
+--------------+--------------+-----------------------------------------------------------------+
| @Incoming    | Method       | @Incoming(schema="schema/song.json")                            |
+--------------+--------------+-----------------------------------------------------------------+
| @Outgoing    | Method       | @Outgoing(code=200, schema="schema/song.json")                  |
+--------------+--------------+-----------------------------------------------------------------+
| @Version     | Class        | @Version(version="3", status=PSX\\Api\\Resource::STATUS_ACTIVE) |
+--------------+--------------+-----------------------------------------------------------------+

In case you define a ``@Version`` annotation you can also assign the "version" 
attribute to every annotation i.e. ``@PathParam(name="foo", type="integer", version=2)``.
Through this it is possible to add annotations only for a specific version.

RAML
----

It is possible to use an existing RAML (http://raml.org/) specification to build 
the API endpoint. In the following a sample API with the fitting RAML 
specification.

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

            // @TODO return the result i.e. from a database

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
          201:
            body:
              application/json:
                schema: !include schema/message.json

Programmatic
------------

It is also possible to manually create the schema definition. In the following a 
schema API which defines the resources.

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

