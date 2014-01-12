
Tutorial
========

In this chapter we develop step by step a simple RESTful API with PSX. This 
should give you a basic overview how PSX works

Prolog
------

The core of every API in PSX is an handler system which knows howto CRUD data
from an specific data source. In our example we want create an API from an mysql
database therefor we use the database handler. PSX offers many handler to read 
from different datasources like Mongodb, Doctrine, DOM, etc.

Creating the table
------------------

Because we want create an API from an database we need to create the fitting
table

.. code-block:: sql

    CREATE TABLE IF NOT EXISTS `news` (
      `id` int(10) NOT NULL AUTO_INCREMENT,
      `userId` int(10) NOT NULL,
      `title` varchar(128) NOT NULL,
      `text` text NOT NULL,
      `date` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

The database handler needs some meta informations about the table like the
table name and what columns are available. We can write these meta informations
in a Table class which can be used by the handler. PSX can obtains these meta
informations also from other sources like an doctrine entity or an mysql 
describe command or you can write your own reader which could get the 
informations from an XML file or something else

.. code-block:: php

    <?php
    
    namespace Test\News;
    
    use PSX\Sql\TableAbstract;
    
    class Table extends TableAbstract
    {
        public function getName()
        {
            return 'news';
        }
    
        public function getColumns()
        {
            return array(
                'id' => self::TYPE_INT | 10 | self::PRIMARY_KEY | self::AUTO_INCREMENT,
                'userId' => self::TYPE_INT | 10,
                'title' => self::TYPE_VARCHAR | 64,
                'text' => self::TYPE_TEXT,
                'date' => self::TYPE_DATETIME,
            );
        }
    
        /*
        public function getConnection()
        {
            return array(
                'userId' => 'users'
            );
        }
        */
    }

Creating the handler
--------------------

The handler is a concept similar to a repository in doctrine which offers an
interface to CRUD records on an given datasource. If a handler implements the
PSX\\Handler\\HandlerQueryInterface you can query records from the datasource 
and if the handler also implements the PSX\\Handler\\HandlerManipulationInterface
you can create, update and delete records. In our case we use the database
handler. We only have to define wich table we want to use for our API and wich
fields should be selected by default. It is also possible to join on multiple 
tables

.. code-block:: php

    <?php
    
    namespace Test\News;
    
    use PSX\Data\HandlerAbstract;
    
    class Handler extends DatabaseHandlerAbstract
    {
        protected function getDefaultSelect()
        {
            $this->manager->getTable('Sample\News\Table')
                ->select(array('id', 'userId', 'title', 'text', 'date'));
        }
    }

Creating the record
-------------------

If we want create, update or delete records we have to define a record class. 
This record class is used if someone makes an POST, PUT or DELETE request. The
body of the request gets imported into the record by calling the fitting setter
methods. PSX parses the annotations and converts the parameter to the fitting 
type. I.e. a DateTime object is automatically created from the value. See 
:doc:`import_data` for more informations howto import complex data structures

.. code-block:: php

    <?php
    
    namespace Test\News;
    
    use DateTime;
    use PSX\Data\RecordAbstract;
    
    class Record extends RecordAbstract
    {
        protected $id;
        protected $userId;
        protected $title;
        protected $text;
        protected $date;
    
        /**
         * @param integer $id
         */
        public function setId($id)
        {
            $this->id = $id;
        }
                
        public function getId()
        {
            return $this->id;
        }

        /**
         * @param integer $userId
         */
        public function setUserId($userId)
        {
            $this->userId = $userId;
        }
            
        public function getUserId()
        {
            return $this->userId;
        }

        /**
         * @param string $title
         */
        public function setTitle($title)
        {
            $this->title = $title;
        }
        
        public function getTitle()
        {
            return $this->title;
        }

        /**
         * @param string $text
         */
        public function setText($text)
        {
            $this->text = $text;
        }
    
        public function getText()
        {
            return $this->text;
        }

        /**
         * @param DateTime $date
         */
        public function setDate(DateTime $date)
        {
            $this->date = $date;
        }
    
        public function getDate()
        {
            return $this->date;
        }
    }

The API endpoint
----------------

Now we have to create the controller wich routes the request to the handler. We
have to add a route to the route file i.e.::

    GET /api Test\News\Application\Api

We extend the HandlerApiAbstract controller where we only have to return our 
handler. In order to create an ATOM feed we have to overwrite the method
getAtomRecord to convert out collection into an atom record

.. code-block:: php

    <?php
    
    namespace Test\News\Application;
    
    use DateTime;
    use PSX\Atom;
    use PSX\Atom\Entry;
    use PSX\Atom\Text;
    use PSX\Data\Record\Mapper;
    use PSX\Data\Record\Mapper\Rule;
    use PSX\Data\RecordInterface;
    use PSX\Module\HandlerApiAbstract;
    use PSX\Util\Uuid;
    
    class Api extends HandlerApiAbstract
    {
        /**
         * Returns the handler on wich the API should operate
         *
         * @return PSX\Handler\HandlerInterface
         */
        protected function getDefaultHandler()
        {
            return $this->getDatabaseManager()
                        ->getHandler('Test\News\Handler');
        }
    
        /**
         * If we want display an atom feed we need to convert our record to an 
         * Atom\Record
         *
         * @param PSX\Data\RecordInterface $result
         * @return PSX\Atom
         */
        protected function getAtomRecord(RecordInterface $result)
        {
            $atom = new Atom();
            $atom->setTitle('Test news');
            $atom->setId(Uuid::nameBased($this->config['psx_url']));
            $atom->setUpdated($result->current()->getDate());
    
            $mapper = new Mapper();
            $mapper->setRule(array(
                'id'       => 'id',
                'title'    => 'title',
                'text'     => new Rule('summary', function($text){
                    return new Text($text, 'text');
                }),
                'date'     => 'updated',
            ));
    
            foreach($result as $row)
            {
                $entry = new Atom\Entry();
                $mapper->map($row, $entry);
    
                $atom->add($entry);
            }
    
            return $atom;
        }
    }
