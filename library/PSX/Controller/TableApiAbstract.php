<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Controller;

use PSX\Api\Documentation;
use PSX\Api\Resource;
use PSX\Api\Version;
use PSX\Data\Record\Merger;
use PSX\Data\RecordInterface;
use PSX\Data\Schema;
use PSX\Data\Schema\Builder as SchemaBuilder;
use PSX\Data\Schema\Property;
use PSX\Data\SchemaInterface;
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
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link	http://phpsx.org
 * @deprecated
 */
abstract class TableApiAbstract extends SchemaApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Sql\TableManager
     */
    protected $tableManager;

    public function getDocumentation()
    {
        $table      = $this->getTableSchema($this->getTable());
        $collection = $this->getCollectionSchema($table);
        $message    = $this->getMessageSchema();

        $path       = $this->context->get(Context::KEY_PATH);
        $resource   = new Resource(Resource::STATUS_ACTIVE, $path);

        $method = new Resource\Get();
        $method->addQueryParameter(Property::getInteger('startIndex'));
        $method->addQueryParameter(Property::getInteger('count'));
        $method->addQueryParameter(Property::getInteger('totalResults'));
        $method->addResponse(200, $collection);

        $resource->addMethod($method);

        $method = new Resource\Post();
        $method->setRequest($table);
        $method->addResponse(200, $message);

        $resource->addMethod($method);

        $method = new Resource\Put();
        $method->setRequest($table);
        $method->addResponse(200, $message);

        $resource->addMethod($method);

        $method = new Resource\Delete();
        $method->setRequest($table);
        $method->addResponse(200, $message);

        $resource->addMethod($method);

        return new Documentation\Simple($resource);
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

    protected function doPost(RecordInterface $record, Version $version)
    {
        $table = $this->getTable();
        $table->create($record);

        return array(
            'success' => true,
            'message' => sprintf('%s successful created', ucfirst($table->getDisplayName())),
        );
    }

    protected function doPut(RecordInterface $record, Version $version)
    {
        $table = $this->getTable();
        $data  = $table->get($record->getRecordInfo()->getField($table->getPrimaryKey()));

        if (empty($data)) {
            throw new StatusCode\NotFoundException('Record not found');
        }

        $table->update(Merger::merge($data, $record));

        return array(
            'success' => true,
            'message' => sprintf('%s successful updated', ucfirst($table->getDisplayName())),
        );
    }

    protected function doDelete(RecordInterface $record, Version $version)
    {
        $table = $this->getTable();
        $data  = $table->get($record->getRecordInfo()->getField($table->getPrimaryKey()));

        if (empty($data)) {
            throw new StatusCode\NotFoundException('Record not found');
        }

        $table->delete($record);

        return array(
            'success' => true,
            'message' => sprintf('%s successful deleted', ucfirst($table->getDisplayName())),
        );
    }

    /**
     * Returns the table on which the handler should operate
     *
     * @return \PSX\Sql\TableInterface
     */
    abstract protected function getTable();

    protected function getTableSchema(TableInterface $table)
    {
        $columns = $table->getColumns();
        $builder = new SchemaBuilder($table->getDisplayName());

        foreach ($columns as $column => $type) {
            $type = ((($type >> 20) & 0xFF) << 20);

            switch ($type) {
                case TableInterface::TYPE_SMALLINT:
                case TableInterface::TYPE_INT:
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

                case TableInterface::TYPE_BIGINT:
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
