
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

    use PSX\Framework\Controller\ControllerAbstract;

    class NewsController extends ControllerAbstract
    {
    	/**
    	 * @Inject
    	 * @var \PSX\Sql\TableManager
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

.. code-block:: php

    <?php

    interface TableQueryInterface
    {
        /**
         * Returns an array of records matching the conditions
         *
         * @param integer $startIndex
         * @param integer $count
         * @param string $sortBy
         * @param integer $sortOrder
         * @param \PSX\Sql\Condition $condition
         * @return array
         */
        public function getAll($startIndex = null, $count = null, $sortBy = null, $sortOrder = null, Condition $condition = null, Fields $fields = null);

        /**
         * Returns an array of records matching the condition
         *
         * @param \PSX\Sql\Condition $condition
         * @return array
         */
        public function getBy(Condition $condition, Fields $fields = null);

        /**
         * Returns an record by the condition
         *
         * @param \PSX\Sql\Condition $condition
         * @return \PSX\Record\RecordInterface
         */
        public function getOneBy(Condition $condition, Fields $fields = null);

        /**
         * Returns an record by the primary key
         *
         * @param string $id
         * @return \PSX\Record\RecordInterface
         */
        public function get($id, Fields $fields = null);

        /**
         * Returns all available fields of this handler
         *
         * @return array
         */
        public function getSupportedFields();

        /**
         * Returns the number of rows matching the given condition in the resultset
         *
         * @param \PSX\Sql\Condition $condition
         * @return integer
         */
        public function getCount(Condition $condition = null);
    }

.. code-block:: php

    interface TableManipulationInterface
    {
        /**
         * Create the record
         *
         * @param \PSX\Record\RecordInterface|array $record
         * @return void
         */
        public function create($record);

        /**
         * Update the record
         *
         * @param \PSX\Record\RecordInterface|array $record
         * @return void
         */
        public function update($record);

        /**
         * Delete the record
         *
         * @param \PSX\Record\RecordInterface|array $record
         * @return void
         */
        public function delete($record);
    }

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

PSX does not force you to use the table classes. If you want use an ORM or any
other database abstraction layer you only have to add the fitting service to the
dependency container in order to use them in your controller. See the chapter
:doc:`/design/dependency_injection` for more informations.
