
Schema
======

Abstract
--------

This chapter gives an overview howto build an REST API around an schema.

API
---

In order to create an API around an schema definition you have to define the
request and response data schemas for each method. For the GET method you 
obviously dont need an request schema. The do* methods must return an array
in the format of the schema. Properties which are not available in the schema
will be skipped. It is possible to generate automatically an API documentation
based on the defined schema documentation

.. code-block:: php

    <?php

    namespace Foo\Application;

    use PSX\Api\Documentation;
    use PSX\Api\Version;
    use PSX\Api\View;
    use PSX\Data\RecordInterface;
    use PSX\Data\Schema\Documentation;
    use PSX\Controller\SchemaApiAbstract;

    class NewsApi extends SchemaApiAbstract
    {
        public function getSchemaDocumentation()
        {
            $responseSchema = $this->schemaManager->getSchema('Foo\Blog\Schema\SuccessMessage');

            $view = new View();
            $view->setGet($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Collection'));
            $view->setPost($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Create'), $responseSchema);
            $view->setPut($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Update'), $responseSchema);
            $view->setDelete($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Delete'), $responseSchema);

            return new Documentation\Simple($view);
        }

        protected function doGet()
        {
            // @TODO the response can come from an database or any other source

            return array(
                'entry' => array(
                    array(
                        'title' => 'lorem ipsum'
                    )
                )
            );
        }

        protected function doCreate(RecordInterface $record)
        {
            // @TODO work with the $record

            return array(
                'success' => true,
                'message' => 'You have successful create a record'
            );
        }

        protected function doUpdate(RecordInterface $record)
        {
            // @TODO work with the $record

            return array(
                'success' => true,
                'message' => 'You have successful update a record'
            );
        }

        protected function doDelete(RecordInterface $record)
        {
            // @TODO work with the $record

            return array(
                'success' => true,
                'message' => 'You have successful delete a record'
            );
        }
    }

More informations about the schema concept at :doc:`../concept/schema`