
API versioning
==============

Abstract
--------

This chapter explains short why it is important to version your API and shows 
possibilties how to implement versioning in PSX

Motivation
----------

If your API has consumers they rely on an specific request/response format. If 
this format changes they probably wont work anymore. In order to be able to make 
changes to an API you have to give the consumers the chance to upgrade there 
code to the new format. This is where versioning comes into play. Each consumer
specifies an specific version when requesting the API. If you want to change the
API format you have to create a new version and deprecate the old one. You 
should communicate to your users that the API will be deprecated in an specific
timeframe so that they can smoothly transition to the new version

Url versioning
--------------

The most easiest solution to versioning is to simply provide multiple routes 
to an API i.e.

.. code-block:: none

    GET /api/v1/news Acme\News\Lion\Api
    GET /api/v2/news Acme\News\Zebra\Api

While this is easy to implement it has the disadvantage that you have multiple
presentations for the same resource. I.e. /api/v1/news and /api/v2/news 
represent the same resource but in different formats

Accept-Header versioning
------------------------

PSX comes with support to handle API versioning throught the Accept header 
field. That means you must specify an explicit version in the Accept header i.e.

.. code-block:: none

    Accept: application/vnd.foobar.v2+json

In the following a short example howto add versioning to an schema API

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
            $documentation  = new Documentation\Version();
            $responseSchema = $this->schemaManager->getSchema('Foo\Blog\Schema\SuccessMessage');

            $view = new View(View::STATUS_DEPRECATED, $this->context->get('psx.path'));
            $view->setGet($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Collection'));
            $view->setPost($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Create'), $responseSchema);
            $view->setPut($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Update'), $responseSchema);
            $view->setDelete($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Delete'), $responseSchema);

            $documentation->addView(1, $view);

            $view = new View();
            $view->setGet($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Collection'));
            $view->setPost($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Create'), $responseSchema);
            $view->setPut($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Update'), $responseSchema);
            $view->setDelete($this->schemaManager->getSchema('PSX\Controller\Foo\Schema\Delete'), $responseSchema);

            $documentation->addView(2, $view);

            return $documentation;
        }

        protected function doGet(Version $version)
        {
        }

        protected function doCreate(RecordInterface $record, Version $version)
        {
        }

        protected function doUpdate(RecordInterface $record, Version $version)
        {
        }

        protected function doDelete(RecordInterface $record, Version $version)
        {
        }
    }

If the consumer requests version 1 an "Warning" header will be added that this
version is deprecated. If no version is specified the latest API version gets
used but it is _strongly_ recommended that users specify an concret version.
