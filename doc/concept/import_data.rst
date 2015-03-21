
Import data
===========

Abstract
--------

This chapter will explain more detailed how you can use an importer to read 
request data.

Record
------

A record is a simple object which stores key value pairs. The importer needs to 
know which fields are available and what type has each field. The record 
importer obtains theses meta informations from the annotation of each method. As 
example lets say we want import the following xml from an request

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>
    <entry>
        <id>1</id>
        <title>foobar</title>
        <user>
            <id>1</id>
            <name>foo</name>
        </user>
        <date>2014-01-12 11:11:53</date>
    </entry>

In this case we have to define the following records

.. code-block:: php

    <?php
    
    namespace Test\News;
    
    use DateTime;
    use PSX\Data\RecordAbstract;
    
    class NewsRecord extends RecordAbstract
    {
        protected $id;
        protected $title;
        protected $user;
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
         * @param Test\News\UserRecord $user
         */
        public function setUser(UserRecord $user)
        {
            $this->user = $user;
        }
    
        public function getUser()
        {
            return $this->user;
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

.. code-block:: php

    <?php
    
    namespace Test\News;
    
    use PSX\Data\RecordAbstract;
    
    class UserRecord extends RecordAbstract
    {
        protected $id;
        protected $name;
    
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
         * @param string $name
         */
        public function setName($name)
        {
            $this->name = $name;
        }
        
        public function getName()
        {
            return $this->name;
        }
    }

If the @param annotation is defined the importer will cast the variable to the 
specific type if it is a scalar type: string, float, integer or boolean. In 
every other case we check whether the given name is a valid class. If the class 
is an RecordInterface we import the data into this record else we pass the data 
as first argument to the constructor.

Through the method getRecordInfo()->getFields() we get an array of all available 
key value pairs of the record. By default this includes every defined property 
of the class which doesnt start with "_". You can override this method if the 
field names are not like your property names

Schema
------

A schema is a general representation of your data format written in PHP. Lets 
say we want import the request data.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>
    <entry>
        <id>1</id>
        <title>foobar</title>
        <user>
            <id>1</id>
            <name>foo</name>
        </user>
        <date>2014-01-12 11:11:53</date>
    </entry>

Therefor we need to define the following schema classes.

.. code-block:: php

    <?php

    class Entry extends SchemaAbstract
    {
        public function getDefinition()
        {
            $sb = $this->getSchemaBuilder('entry');
            $sb->integer('id');
            $sb->string('title')
                ->setPattern('[A-z]+');
            $sb->complexType($this->getSchema('User'));
            $sb->dateTime('date');

            return $sb->getProperty();
        }
    }

.. code-block:: php

    <?php
    
    class User extends SchemaAbstract
    {
        public function getDefinition()
        {
            $sb = $this->getSchemaBuilder('user');
            $sb->integer('id');
            $sb->string('name')
                ->setPattern('[A-z]+');

            return $sb->getProperty();
        }
    }

With the schema you can define a much finer representation of your data model.
More informations about the schema concept at :doc:`schema`.

Entity
------

PSX has also an entity importer which get the meta informations from the 
annotations of an entity. At the moment the entity importer will not lookup
any references means that only the available fields of the entity will be 
imported. In the following an example howto use an entity.

.. code-block:: php

    <?php

    namespace Foo\Application;

    use PSX\ControllerAbstract;

    class Index extends ControllerAbstract
    {
        public function doIndex()
        {
            $entry = $this->import(new FooEntity());

            // do something with the entry
        }
    }

Writing a custom importer
-------------------------

The meta informations how the request data looks can be obtained from different
sources. If you have already meta data in your project about your models you
could write your own importer. The importer has an accept method which checks
whether the source is valid to get passed to the import method. The import 
method takes the response from the reader and creates an record based on the
meta data from the source.

.. literalinclude:: ../../library/PSX/Data/Record/ImporterInterface.php
   :language: php
   :lines: 31-52
   :prepend: <?php

The importer must be added to the importer manager in the DI container. After
that you can use your own class in the import method inside the controller
