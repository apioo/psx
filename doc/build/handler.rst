
Handler
=======

Abstract
--------

This chapter gives an overview howto build an REST API around an handler.

API
---

Creating an API through an handler is the easiest way to create an REST API
in PSX since the handler provides already all CRUD capabilities.

.. code-block:: php

    namespace Foo\Application;

    use PSX\Controller\HandlerApiAbstract;
    use PSX\Filter;
    use PSX\Validate;
    use PSX\Validate\RecordValidator;
    use PSX\Validate\Property;

    class NewsApi extends HandlerApiAbstract
    {
        /**
         * @Inject
         * @var PSX\Sql\TableManager
         */
        protected $tableManager;

        protected function getValidator()
        {
            return new RecordValidator($this->validate, array(
                new Property('id', Validate::TYPE_INTEGER),
                new Property('userId', Validate::TYPE_INTEGER),
                new Property('title', Validate::TYPE_STRING, array(new Filter\Length(3, 16))),
                new Property('date', Validate::TYPE_STRING, array(new Filter\DateTime())),
            ));
        }

        protected function getHandler()
        {
            return $this->tableManager->getTable('Foo\NewsTable');
        }
    }

In our example we use the table manager which returns an table handler. More
informations about handlers at :doc:`../concept/handler`.

