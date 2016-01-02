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

namespace PSX\Sql\Table\Reader;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;
use PSX\Sql\Table\Definition;
use PSX\Sql\Table\ReaderInterface;
use PSX\Sql\TableInterface;

/**
 * EntityAnnotation
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class EntityAnnotation implements ReaderInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getTableDefinition($class)
    {
        $metaData = $this->em->getMetadataFactory()->getMetadataFor($class);
        $columns  = $this->getEntityColumns($metaData);

        return new Definition($metaData->getTableName(), $columns);
    }

    protected function getEntityColumns(ClassMetadata $metaData)
    {
        $columns = $metaData->getColumnNames();
        $result  = array();

        foreach ($columns as $columnName) {
            $type = $this->getColumnTypeValue($metaData->getTypeOfField($columnName));

            if ($metaData->isIdentifier($metaData->getFieldName($columnName))) {
                $type|= TableInterface::PRIMARY_KEY;

                if ($metaData->isIdGeneratorIdentity() || $metaData->isIdGeneratorSequence()) {
                    $type|= TableInterface::AUTO_INCREMENT;
                }
            }

            $result[$columnName] = $type;
        }

        return $result;
    }

    protected function getColumnTypeValue($type)
    {
        switch ($type) {
            case 'integer':
                return TableInterface::TYPE_INT;

            case 'smallint':
                return TableInterface::TYPE_SMALLINT;

            case 'bigint':
                return TableInterface::TYPE_BIGINT;

            case 'text':
            case 'array':
            case 'object':
                return TableInterface::TYPE_TEXT;

            case 'decimal':
                return TableInterface::TYPE_DECIMAL;

            case 'boolean':
                return TableInterface::TYPE_BOOLEAN;

            case 'datetime':
                return TableInterface::TYPE_DATETIME;

            case 'date':
                return TableInterface::TYPE_DATE;

            case 'time':
                return TableInterface::TYPE_TIME;

            case 'float':
                return TableInterface::TYPE_FLOAT;

            default:
            case 'string':
                return TableInterface::TYPE_VARCHAR;
        }
    }
}
