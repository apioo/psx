
Handler
=======

Abstract
--------

This chapter should give you an overview what the handler system is how it works
and what handler are avaialble. To see howto build an API based on an handler
go to :doc:`../build/handler`

Design
------

An handler is an object which offers a simple interface to CRUD records. The 
handler should simplify creating an API based on various data sources. There are
two interfaces which an handler can implement. The HandlerQueryInterface which
provides method for data retrieval

.. literalinclude:: ../../library/PSX/Handler/HandlerQueryInterface.php
   :language: php
   :lines: 35-88
   :prepend: <?php

and the HandlerManipulationInterface to create, update or delete an record

.. literalinclude:: ../../library/PSX/Handler/HandlerManipulationInterface.php
   :language: php
   :lines: 35-57
   :prepend: <?php

Table handler
-------------

The table handler operates on an sql table. You can obtain an table through the
table manager

.. code-block:: php

    <?php
    
    class FooController
    {
        /**
         * @Inject
         * @var PSX\Sql\TableManager
         */
        protected $tableManager;

        public function doIndex()
        {
            $table = $this->tableManager->getTable('Foo\Table');

            // @TODO work with $table
        }
    }

The table class provides informations how the table is structured.

.. code-block:: php

    <?php

    use PSX\Sql\TableAbstract;

    class Table extends TableAbstract
    {
        public function getName()
        {
            return 'foo';
        }

        public function getColumns()
        {
            return array(
                'id'    => self::TYPE_INT | 11 | self::PRIMARY_KEY,
                'title' => self::TYPE_VARCHAR | 32,
                'date'  => self::TYPE_DATETIME
            );
        }
    }

It is possible to register an table reader class which provides these table 
informations. By default the table name and available columns are hard coded 
into an class but you could also use i.e. the mysql describe reader which 
obtains these informations from an describe query.

Select handler
--------------

With the select handler you can join over multiple tables. 

.. code-block:: php

    <?php
    
    class FooController
    {
        /**
         * @Inject
         * @var PSX\Sql\TableManager
         */
        protected $tableManager;

        public function doIndex()
        {
            $select = $this->tableManager->getTable('Foo\Table')
                        ->select(array('id', 'title'), 'news')
                        ->join(Join::INNER, $this->tableManager->getTable('Foo\User')
                            ->select(array('name'), 'user')
                        )
                        ->orderBy('id', Sql::SORT_DESC)
                        ->limit(8);

            // @TODO work with $select
        }
    }

Database handler
----------------

The database handler is for making raw SQL queries. This handler implements only
the HandlerQueryInterface that means we can not create, update or delete records

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
            return 'SELECT `id`, `userId`, `title`, `date` FROM `psx_handler_comment` {condition} {orderBy} {limit}';
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
a detailed list of all required methods
