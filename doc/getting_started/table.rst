
Table
=====

PSX provides a table controller which can create an REST endpoint to CRUD data
on an table. The table API extends the schema API but gets all needed schema 
informations from an sql table.

Definition
----------

In order to create an API PSX needs meta informations about the table. By 
default PSX uses an simple table class which contains such informations. Here an 
example how a simple table can look:

.. code-block:: php

    <?php
    
    namespace Acme\Table;
    
    use PSX\Sql\TableAbstract;
    
    class News extends TableAbstract
    {
        public function getName()
        {
            return 'news_table';
        }
    
        public function getColumns()
        {
            return array(
                'id' => self::TYPE_INT | self::PRIMARY_KEY,
                'userId' => self::TYPE_INT,
                'title' => self::TYPE_VARCHAR,
                'date' => self::TYPE_DATETIME,
            );
        }
    }

You can automatically generate a table class with the following command. Note to 
make this work the connection credentials must be given in the :code:`psx_sql_*` 
config keys.

.. code::

    $ ./vendor/bin/psx generate:table Acme\Table\News news_table

API
---

In the following an table API which provides basic CRUD operations on the given
table.

.. code-block:: php

    <?php

    namespace Acme\Api\News;

    use PSX\Controller\TableApiAbstract;

    class Endpoint extends TableApiAbstract
    {
        public function getTable()
        {
            return $this->tableManager->getTable('Acme\Table\News');
        }
    }

