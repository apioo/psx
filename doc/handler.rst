
Handler
=======

Abstract
--------

This chapter gives an overview howto build an REST API around an handler. An 
handler is an concept in PSX which offers a simple interface to CRUD records.

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
informations about the table manager at the table chapter. Beside that PSX
offers other handler

Database handler
----------------

The database handler is for making raw SQL queries. This handler implements only
the HandlerQueryInterface that means we can not CUD records

.. code-block:: php

    <?php
    
    class TestHandler extends DatabaseHandlerAbstract
    {
        public function getMapping()
        {
            return new Mapping($this->getQuery(), array(
                'id'     => MappingAbstract::TYPE_INTEGER | 10 | MappingAbstract::ID_PROPERTY,
                'userId' => MappingAbstract::TYPE_INTEGER | 10,
                'title'  => MappingAbstract::TYPE_STRING | 32,
                'date'   => MappingAbstract::TYPE_DATETIME,
            ));
        }

        protected function getQuery()
        {
            return 'SELECT {fields} FROM `psx_handler_comment` {condition} {orderBy} {limit}';
        }
    }


Doctrine handler
----------------

The doctrine handler uses the entity manager to create a query builder. The 
handler gets all available fields from this query

.. code-block:: php

    <?php
    
    class TestHandler extends DoctrineHandlerAbstract
    {
        protected function getMapping()
        {
            return $this->manager->createQueryBuilder()
                ->from('PSX\Handler\Doctrine\TestEntity', 'comment');
        }
    }


Dom handler
-----------

The DOM handler uses an DOMDocument. As mapping you define the source document, 
the root key of the entries element, the name auf the entry element and also the 
available fields

.. code-block:: php

    <?php
    
    class Handler extends DomHandlerAbstract
    {
        public function getMapping()
        {
            $dom = new DOMDocument();
            $dom->loadXml('<xml />');

            return new Mapping($dom, 'comments', 'comment', array(
                'id'     => MappingAbstract::TYPE_INTEGER | 10 | MappingAbstract::ID_PROPERTY,
                'userId' => MappingAbstract::TYPE_INTEGER | 10,
                'title'  => MappingAbstract::TYPE_STRING | 32,
                'date'   => MappingAbstract::TYPE_DATETIME,
            ));
        }
    }


Map handler
-----------

The map handler can be used to build an handler from an array. This is useful
if you obtain the data i.e. from an API or other source where the data is 
available as array

.. code-block:: php

    <?php

    class Handler extends MapHandlerAbstract
    {
        public function getMapping()
        {
            $data = array();

            return new Mapping($data, array(
                'id'     => MappingAbstract::TYPE_INTEGER | 10 | MappingAbstract::ID_PROPERTY,
                'userId' => MappingAbstract::TYPE_INTEGER | 10,
                'title'  => MappingAbstract::TYPE_STRING | 32,
                'date'   => MappingAbstract::TYPE_DATETIME,
            ));
        }
    }


Mongodb handler
---------------

The mongodb handler can select records from an mongodb collection. The mapping 
returns the MongoCollection and the mapping of the available fields

.. code-block:: php

    <?php

    class Handler extends MongodbHandlerAbstract
    {
        public function getMapping()
        {
            return new Mapping($this->client->selectCollection('psx', 'psx_handler_comment'), array(
                'id'     => MappingAbstract::TYPE_INTEGER | 10 | MappingAbstract::ID_PROPERTY,
                'userId' => MappingAbstract::TYPE_INTEGER | 10,
                'title'  => MappingAbstract::TYPE_STRING | 32,
                'date'   => MappingAbstract::TYPE_DATETIME,
            ));
        }
    }

ProxyCache handler
------------------

The proxy cache handler is a handler which caches the result of an specific 
mapper. That means you can wrap the proxy cache handler around an database 
handler or any other handler to cache the results

Write your own handler
----------------------

If you want write an handler you have to implement either the 
PSX\\Handler\\HandlerManipulationInterface, PSX\\Handler\\HandlerQueryInterface
or both which means you implement the PSX\\Handler\\HandlerInterface. If you
implement the HandlerManipulationInterface your handler must be able to create,
update or delete records. If you implement the HandlerQueryInterface your
handler must support retrieval of records please see the API documentation for
detailed list of all required methods
