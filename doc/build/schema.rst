
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
will be skipped

.. code-block:: php

    <?php

    namespace Foo\Application;

    use PSX\Data\RecordInterface;
    use PSX\Data\Schema\ApiDocumentation;
    use PSX\Controller\SchemaApiAbstract;

    class NewsApi extends SchemaApiAbstract
    {
        public function getSchemaDocumentation()
        {
            $responseSchema = $this->schemaManager->getSchema('PSX\Controller\Foo\Schema\SuccessMessage');

            $doc = new ApiDocumentation();
            $doc->setGet($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Collection'));
            $doc->setPost($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Create'), $responseSchema);
            $doc->setPut($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Update'), $responseSchema);
            $doc->setDelete($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Delete'), $responseSchema);

            return $doc;
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