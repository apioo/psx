
Schema
======

In order to build an schema API with PSX you need to define an request and 
response schema which describes the structure of the data. Because of this PSX 
can automatically validate incomming data and format outgoing data according to 
the schema. Also it is possible to generate documentation or other schema 
formats like i.e. Swagger, WSDL or RAML based on the defined schema.

Definition
----------

Here an example how a simple schema can look:

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

It is also possible to generate such an schema from an sql database. You can use 
the following command:

.. code::

    $ ./vendor/bin/psx generate:schema Acme\Schema\News news_table

API
---

To build an API we need to assign the schema to an request method. Therefore 
the API knows the schema for every incomming request and outgoing response. In 
the following a simple schema API controller.

.. code-block:: php

    <?php

    namespace Acme\Api\News;

    use PSX\Api\Documentation;
    use PSX\Api\Version;
    use PSX\Api\View;
    use PSX\Controller\SchemaApiAbstract;
    use PSX\Data\RecordInterface;
    use PSX\Loader\Context;

    class Endpoint extends SchemaApiAbstract
    {
        public function getDocumentation()
        {
            $responseMessage = $this->schemaManager->getSchema('Acme\Schema\ResponseMessage');

            $builder = new View\Builder(View::STATUS_ACTIVE, $this->context->get(Context::KEY_PATH));
            $builder->setGet($this->schemaManager->getSchema('Acme\Schema\Collection'));
            $builder->setPost($this->schemaManager->getSchema('Acme\Schema\Create'), $responseMessage);
            $builder->setPut($this->schemaManager->getSchema('Acme\Schema\Update'), $responseMessage);
            $builder->setDelete($this->schemaManager->getSchema('Acme\Schema\Delete'), $responseMessage);

            return new Documentation\Simple($builder->getView(), 'Sample API');
        }

        protected function doGet(Version $Version)
        {
            // @TODO i.e. return the result from an database

            return [
                'entry' => [
                    [
                        'userId' => 'Foobar',
                        'title'  => 'Foobar',
                        'date'   => '2014-08-10',
                    ]
                ],
            ];
        }

        protected function doCreate(RecordInterface $record, Version $Version)
        {
            // @TODO i.e. insert the record into an database

            return [
                'success' => true,
                'message' => 'Successful created',
            ];
        }

        protected function doUpdate(RecordInterface $record, Version $Version)
        {
            // @TODO i.e. update the record on the database

            return [
                'success' => true,
                'message' => 'Successful updated',
            ];
        }

        protected function doDelete(RecordInterface $record, Version $Version)
        {
            // @TODO i.e. delete the record from the database

            return [
                'success' => true,
                'message' => 'Successful deleted',
            ];
        }
    }
