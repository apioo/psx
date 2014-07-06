
Handler concept
===============

As stated in the tutorial the core of every PSX api is the handler system. This
chapter will explain how different handler work and howto write your own handler

Database handler
----------------

The database handler returns an PSX\\Table\\Select object which gives the 
handler all informations about the table and all available fields

.. code-block:: php

    <?php
    
    class Handler extends DatabaseHandlerAbstract
    {
        public function getDefaultSelect()
        {
            return $this->manager->getTable('Test\Table')
                ->select(array('id', 'userId', 'title', 'date'));
        }
    }


Doctrine handler
----------------

The doctrine handler uses the entity manager to create a query builder. The 
handler gets all available fields from this query

.. code-block:: php

    <?php
    
    class Handler extends DoctrineHandlerAbstract
    {
        protected function getDefaultSelect()
        {
            return $this->manager->createQueryBuilder()
                ->from('Test\Entity', 'entity');
        }
    }


Dom handler
-----------

The DOM handler uses the DOMDocument to query the records. As mapping you define
the source document, the root key of the entries element, the name auf the entry 
element and also the available fields

.. code-block:: php

    <?php
    
    class Handler extends DomHandlerAbstract
    {
        public function getMapping()
        {
            $dom = new DOMDocument();
            $dom->loadXml('');
    
            return new Mapping($dom, 'root', 'entry', array(
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

The mongodb handler can select records from an mongo database. The mapping 
returns the MongoCollection and the mapping of the available fields

.. code-block:: php

    <?php

    class Handler extends MongodbHandlerAbstract
    {
        public function getMapping()
        {
            return new Mapping($this->client->selectCollection('test', 'collection'), array(
                'id'     => MappingAbstract::TYPE_INTEGER | 10 | MappingAbstract::ID_PROPERTY,
                'userId' => MappingAbstract::TYPE_INTEGER | 10,
                'title'  => MappingAbstract::TYPE_STRING | 32,
                'date'   => MappingAbstract::TYPE_DATETIME,
            ));
        }
    }

Pdo handler
-----------

The PDO handler uses native SQL queries through PDO in order to obtain the 
result. The getSelectStatment and getCountStatment method return an PDOStatement

.. code-block:: php

    <?php

    class Handler extends PdoHandlerAbstract
    {
        public function getMapping()
        {
            return new Mapping(array(
                'id'     => MappingAbstract::TYPE_INTEGER | 10 | MappingAbstract::ID_PROPERTY,
                'userId' => MappingAbstract::TYPE_INTEGER | 10,
                'title'  => MappingAbstract::TYPE_STRING | 32,
                'date'   => MappingAbstract::TYPE_DATETIME,
            ));
        }

        protected function getSelectStatment(array $fields, $startIndex = 0, $count = 16, $sortBy = null, $sortOrder = null, Condition $con = null)
        {
        }

        protected function getCountStatment(Condition $con = null)
        {
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
