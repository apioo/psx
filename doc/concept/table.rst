
Table
=====

Abstract
--------

The PSX table classes offern an alternative to an ORM. Each table class
represents an sql table. The table class contains the table name and all 
available columns. It is also the place where you can put all complex business 
queries like in an repository. To get an first impression here an example:

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

Inside an controller you could use the table class in order to get all comment 
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

If you have complex queries it is best practice to put such queries inside the 
table class where you can reuse the method across your application. Here an
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

            return $this->connection->fetchAll($sql, array(
            	'user_id' => $userId,
            ));
        }
    }

Generation
----------

It is possible to generate such table classes from an sql table. Therefor you
can use the following command

.. code::

    $ ./vendor/bin/psx generate:table Acme\Table\Comment psx_handler_comment

