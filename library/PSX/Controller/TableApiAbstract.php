<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Controller;

use PSX\Api\Documentation;
use PSX\Api\Version;
use PSX\Api\View;
use PSX\Api\View\Builder;
use PSX\Data\Schema;
use PSX\Data\SchemaInterface;
use PSX\Data\Schema\Property\ComplexType;
use PSX\Data\RecordInterface;
use PSX\Data\Schema\Builder as SchemaBuilder;
use PSX\Http\Exception as StatusCode;
use PSX\Loader\Context;
use PSX\Sql\TableInterface;
use PSX\Util\Api\FilterParameter;

/**
 * Controller which helps to build an API based on an sql table. The API offers
 * basic CRUD functionality. The table can be automatically generated with:
 * <code>
 * > php bin/psx generate:table Foo\BarTable some_table
 * </code>
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link	http://phpsx.org
 */
abstract class TableApiAbstract extends SchemaApiAbstract
{
	/**
	 * @Inject
	 * @var PSX\Sql\TableManager
	 */
	protected $tableManager;

	public function getDocumentation()
	{
		$table      = $this->getTableSchema($this->getTable());
		$collection = $this->getCollectionSchema($table);
		$message    = $this->getMessageSchema();

		$path    = $this->context->get(Context::KEY_PATH);
		$builder = new Builder(View::STATUS_ACTIVE, $path);
		$builder->setGet($collection);
		$builder->setPost($table, $message);
		$builder->setPut($table, $message);
		$builder->setDelete($table, $message);

		return new Documentation\Simple($builder->getView());
	}

	protected function doGet(Version $version)
	{
		$table     = $this->getTable();
		$parameter = $this->getFilterParameter();
		$condition = FilterParameter::getCondition($parameter);

		return array(
			'startIndex'   => $parameter->getStartIndex() ?: 0,
			'count'        => $parameter->getCount() ?: 16,
			'totalResults' => $table->getCount(),
			'entry'        => $table->getAll(
				$parameter->getStartIndex(),
				$parameter->getCount(),
				$parameter->getSortBy(),
				$parameter->getSortOrder(),
				$condition
			),
		);
	}

	protected function doCreate(RecordInterface $record, Version $version)
	{
		$table = $this->getTable();
		$table->create($record);

		return array(
			'success' => true,
			'message' => 'Record successful created',
		);
	}

	protected function doUpdate(RecordInterface $record, Version $version)
	{
		$table = $this->getTable();
		$table->update($record);

		return array(
			'success' => true,
			'message' => 'Record successful updated',
		);
	}

	protected function doDelete(RecordInterface $record, Version $version)
	{
		$table = $this->getTable();
		$table->delete($record);

		return array(
			'success' => true,
			'message' => 'Record successful deleted',
		);
	}

	/**
	 * Returns the table on which the handler should operate
	 *
	 * @return PSX\Sql\TableInterface
	 */
	abstract protected function getTable();

	protected function getTableSchema(TableInterface $table)
	{
		$columns = $table->getColumns();
		$builder = new SchemaBuilder($table->getName());

		foreach($columns as $column => $type)
		{
			$type = ((($type >> 20) & 0xFF) << 20);

			switch($type)
			{
				case TableInterface::TYPE_SMALLINT:
				case TableInterface::TYPE_INT:
				case TableInterface::TYPE_BIGINT:
					$builder->integer($column);
					break;

				case TableInterface::TYPE_BOOLEAN:
					$builder->boolean($column);
					break;

				case TableInterface::TYPE_DECIMAL:
				case TableInterface::TYPE_FLOAT:
					$builder->float($column);
					break;

				case TableInterface::TYPE_DATE:
					$builder->date($column);
					break;

				case TableInterface::TYPE_DATETIME:
					$builder->dateTime($column);
					break;

				case TableInterface::TYPE_TIME:
					$builder->time($column);
					break;

				case TableInterface::TYPE_VARCHAR:
				case TableInterface::TYPE_TEXT:
				case TableInterface::TYPE_BLOB:
					$builder->string($column);
					break;
			}
		}

		return new Schema($builder->getProperty());
	}

	protected function getCollectionSchema(SchemaInterface $table)
	{
		$builder = new SchemaBuilder('collection');
		$builder->integer('startIndex');
		$builder->integer('count');
		$builder->integer('totalResults');
		$builder->arrayType('entry')
			->setPrototype($table->getDefinition());

		return new Schema($builder->getProperty());
	}

	protected function getMessageSchema()
	{
		$builder = new SchemaBuilder('message');
		$builder->boolean('success');
		$builder->string('message');

		return new Schema($builder->getProperty());
	}
}
