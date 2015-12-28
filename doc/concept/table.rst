
Table
=====

Abstract
--------

The PSX table class provides a simple repository pattern which can be used as 
alternative to an ORM. It works with plain SQL queries. Each table class 
represents a SQL table which contains the table name and all available columns.
A table class returns record objects which are filled with the values from the 
query. It is also the place where you can put all complex business queries. To 
get a first impression here an example:

.. code-block:: php

    <?php

    namespace Acme\Table;

    use PSX\Sql\TableAbstract;

    class Comment extends TableAbstract
    {
        public function getName()
        {
            return 'psx_handler_comment';
        }

        public function getColumns()
        {
            return array(
                'id' => self::TYPE_INT | self::AUTO_INCREMENT | self::PRIMARY_KEY,
                'userId' => self::TYPE_INT,
                'title' => self::TYPE_VARCHAR,
                'date' => self::TYPE_DATETIME,
            );
        }
    }

Inside a controller you could use the table class in order to get all comment 
entries

.. code-block:: php

    <?php

    namespace Acme\News\Application;

    use PSX\ControllerAbstract;

    class NewsController extends ControllerAbstract
    {
    	/**
    	 * @Inject
    	 * @var PSX\Sql\TableManager
    	 */
    	protected $tableManager;

    	public function doIndex()
    	{
    	    $comments = $this->tableManager->getTable('Acme\Table\Comment')->getAll();

    	    $this->setBody(array(
    	    	'comments' => $comments,
    	    ));
    	}
    }

Each table has automatically some standard query methods to query and manipulate
the table

.. literalinclude:: ../../library/PSX/Sql/TableQueryInterface.php
   :language: php
   :lines: 32-82
   :prepend: <?php

.. literalinclude:: ../../library/PSX/Sql/TableManipulationInterface.php
   :language: php
   :lines: 32-57
   :prepend: <?php

If you have complex queries it is best practice to put such queries inside the 
table class where you can reuse the method across your application. Here a
fictional method which returns the best comments

.. code-block:: php

    <?php

    class Comment extends TableAbstract
    {
        // ...

        public function getBestCommentsByUser($userId)
        {
            $sql = '   SELECT id,
                              title,
                              content
                         FROM psx_handler_comment comment
                    LEFT JOIN psx_handler_comment_rating rating
                           ON rating.comment_id = comment.id
                        WHERE comment.user_id = :user_id
                     ORDER BY rating.value DESC
                        LIMIT 8';

            return $this->project($sql, array(
            	'user_id' => $userId,
            ));
        }
    }

PSX does not force you to use the table classes. If you want use a ORM or any
other database abstraction layer you only have to add the fitting service to the
dependency container in order to use them in your controller. See the chapter
:doc:`/design/dependency_injection` for more informations.

Generation
----------

It is possible to generate such table classes from a SQL table. Therefor you
can use the following command

.. code::

    $ ./vendor/bin/psx generate:table Acme\Table\Comment psx_handler_comment

