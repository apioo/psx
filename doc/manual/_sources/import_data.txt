
Import data
===========

Record classes are used in PSX to import data from an request. We use an reader
class to read the request body and then the request gets imported into an record
through an importer class. This chapter explains the default annotation importer 
and howto write your own importer

General
-------

A record is a simple object to store key value pairs. When we use an importer
class the importer needs to know which fields are available and what type has
each field. The default importer obtains theses meta informations from the 
annotation of each method. As example lets say we want import the following xml 
from an request

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

If the @param annotation is defined the default importer will cast the variable
to the specific type if it is a scalar type: string, float, integer or boolean.
In every other case we check whether the given name is a valid class. If the
class is an RecordInterface we import the data into this record else we pass the
data as first argument to the constructor.

Through the method getRecordInfo()->getFields() we get an array of all available 
key value pairs of the record. By default this includes every defined property 
of the class which doesnt start with "_". You can override this method if the 
field names are not like your property names

Using data factories
--------------------

If we have a collection of records and you want to create for each item a 
different record class you can use the FactoryInterface. Lets say we have the
following request body

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>
    <entry>
        <id>1</id>
        <title>foobar</title>
        <items>
            <item>
                <id>1</id>
                <type>article</type>
                <content>foo</content>
            </item>
            <item>
                <id>2</id>
                <type>page</type>
                <content>bar</content>
            </item>
        </items>
    </entry>

In this case we want create for each item the fitting record class depending on
the type value. Therefor we have to define as @param annotation an class wich 
implements the PSX\\Data\\FactoryInterface i.e.

.. code-block:: php

    <?php

    namespace Test\News;

    use PSX\Data\RecordAbstract;

    class Entry extends RecordAbstract
    {
        // ...

        /**
         * @param array<Test\ItemFactory> $items
         */
        public function setItems(array $items)
        {
            $this->items = $items;
        }
    }

The $data wich is passed to the factory method is the complete item entry. In 
our case we check whether a class exists with the type and then return an 
instance of it else we use a default type

.. code-block:: php

    <?php
    
    namespace Test\News;
    
    use PSX\Data\FactoryInterface;
    
    class ItemFactory implements FactoryInterface
    {
        public function factory($data)
        {
            $type  = isset($data['type']) ? $data['type'] : 'article';
            $class = 'Test\Item' . ucrifst($type)
            
            if(class_exists($class))
            {
                return new $class();
            }
            else
            {
                return new ItemArticle();
            }
        }
    }

The importer will use the record object wich gets returned by the factory and
imports the data into the record

Using data builder
------------------

If you want completely overwrite the import mechanism for a specific key you can
use the BuilderInterface. The difference between a factory and the builder is
that the factory returns only the record without any data (the data gets then 
imported through the standard import mechanism) and the builder returns the 
complete record containing any necessary data. Lets look at this example request
body

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>
    <entry>
        <id>1</id>
        <title>foobar</title>
        <item>
            <foo>
                <bar>
                    <title>foo</title>
                </bar>
            </foo>
        </item>
    </entry>

In this example we only want to create one item record containing the title. We 
use the following builder to read the title from the nested xml

.. code-block:: php

    <?php
    
    namespace Test\News;
    
    use PSX\Data\BuilderInterface;
    
    class ItemBuilder implements BuilderInterface
    {
        public function build($data)
        {
            $title = isset($data['item']['foo']['bar']['title']) ? $data['item']['foo']['bar']['title'] : null;

            return new Record('item', array(
                'title' => $title,
            ));
        }
    }

Writing a custom importer
-------------------------

The meta informations how an record is structured can be obtained from different
sources. By default we use the annotation importer but you can also write your
own importer wich gets theses informations from other sources. PSX comes also
with an entity annotation importer wich reads the annotations from an doctrine
entity and creates the fitting record from it. The importer interface has only
a single method

.. literalinclude:: ../library/PSX/Data/Record/ImporterInterface.php
   :language: php
   :lines: 36-44
   :prepend: <?php

The first argument $record is not type hinted since this can be an 
RecordInterface or an entity or an path to an xml file containing the meta 
informations from the record. The $data argument is the result from the reader.
The importer returns then the record containing the data from the reader result.
