
Tutorial
========

This is the main chapter of the manual wich explains step by step howto develop 
a RESTful API based on PSX. In this example we create a simple news API where 
you can create and receive news

Setting up the table
--------------------

For our example we need a simple table called news where all records are 
stored

.. code-block:: sql

    CREATE TABLE IF NOT EXISTS `news` (
      `id` int(10) NOT NULL AUTO_INCREMENT,
      `userId` int(10) NOT NULL,
      `title` varchar(128) NOT NULL,
      `text` text NOT NULL,
      `date` datetime NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

Creating the record
-------------------

The record represents an row from an database. If we post a news to the api 
endpoint this record is created and the setter methods are called. This is the 
place where you have to validate the user data

.. code-block:: php

    <?php
    
    namespace Test\News;
    
    use PSX\Data\RecordAbstract;
    
    class Record extends RecordAbstract
    {
    	protected $id;
    	protected $userId;
    	protected $title;
    	protected $text;
    	protected $date;
    
    	protected $_date;
    
    	public function setId($id)
    	{
    		$this->id = $id;
    	}
    
    	public function setUserId($userId)
    	{
    		$this->userId = $userId;
    	}
    
    	public function setTitle($title)
    	{
    		$this->title = $title;
    	}
    
    	public function setText($text)
    	{
    		$this->text = $text;
    	}
    
    	public function setDate($date)
    	{
    		$this->date = $date;
    	}
    
    	public function getDate()
    	{
    		if($this->_date === null)
    		{
    			$this->_date = new DateTime($this->date);
    		}
    
    		return $this->_date;
    	}
    }

Creating the handler
--------------------

The handler is a concept similar to a repository in doctrine wich abstracts the 
sql queries away from the controller. Instead of creating sql queries you should 
add "getByFoo" methods to the handler. The Handler is also responsible to 
create, update and delete an record. In our case the default select methods wich 
are provided by the HandlerAbstract are sufficient for our api so we dont have 
to add additional methods

.. code-block:: php

    <?php
    
    namespace Test\News;
    
    use PSX\Data\HandlerAbstract;
    
    class Handler extends HandlerAbstract
    {
    	public function getDefaultSelect()
    	{
    		$this->manager->getTable('Sample\News\Table')
    			->select(array('id', 'userId', 'title', 'text', 'date'));
    	}
    }

Creating the table
------------------

Note the key parts of PSX are the records and handler the table class wich we 
now create is only a helper class for the handler in order to retrieve records 
from an mysql table via PDO. You are free to implement your own HandlerInterface 
and use an ORM like doctrine, simple SQL queries or any other system to CRUD 
records.

The table represents an database table. It contains the table name wich columns 
are available and the relations to other tables. In this example we have no 
relation to another table but to give an example the getConnection method is 
implemented

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

The API endpoint
----------------

We create a file called news.php in the module/api folder. This file can be 
accessed via http://localhost/index.php/api/news. We define the onLoad method 
wich is called when the module was loaded.

This is now our REST API endpoint where we can make GET and POST requests. You 
can versioning your API by creating a folder structure i.e. put the news.php in 
the folder "v1" and the endpoint url would be http://localhost/index.php/api/v1/news