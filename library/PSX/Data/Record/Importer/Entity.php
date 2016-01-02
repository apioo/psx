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

namespace PSX\Data\Record\Importer;

use DateTime;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;
use PSX\Data\Record as DataRecord;
use PSX\Data\Record\ImporterInterface;
use ReflectionException;

/**
 * Importer which reads the annotations from an entity and creates an record
 * based on the defined fields
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Entity implements ImporterInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function accept($entity)
    {
        return $this->isEntity($entity);
    }

    public function import($entity, $data)
    {
        if (!$this->isEntity($entity)) {
            throw new InvalidArgumentException('Entity must be an entity');
        }

        if (!$data instanceof \stdClass) {
            throw new InvalidArgumentException('Data must be an stdClass');
        }

        $metaData = $this->em->getMetadataFactory()->getMetadataFor($this->getClassName($entity));
        $fields   = $this->getEntityFields($metaData, (array) $data);

        return new DataRecord($metaData->getTableName(), $fields);
    }

    protected function getEntityFields(ClassMetadata $metaData, array $data)
    {
        // change data keys to camelcase
        $result = array();
        foreach ($data as $key => $value) {
            // convert to camelcase if underscore is in name
            if (strpos($key, '_') !== false) {
                $key = implode('', array_map('ucfirst', explode('_', $key)));
            }

            $result[$key] = $value;
        }
        $data = $result;

        // get all fields
        $fieldNames = $metaData->getFieldNames();
        $fields     = array();

        foreach ($fieldNames as $fieldName) {
            if (!isset($data[$fieldName])) {
                continue;
            }

            $type  = $metaData->getTypeOfField($fieldName);
            $value = $this->getColumnTypeValue($type, $data[$fieldName]);

            $fields[$fieldName] = $value;
        }

        return $fields;
    }

    protected function getColumnTypeValue($type, $value)
    {
        switch ($type) {
            case 'integer':
            case 'smallint':
            case 'bigint':
                return (int) $value;

            case 'decimal':
            case 'float':
                return (float) $value;

            case 'boolean':
                return $value === 'false' ? false : (bool) $value;

            case 'datetime':
            case 'date':
                return new DateTime($value);

            default:
                return $value;
        }
    }

    protected function isEntity($class)
    {
        try {
            return !$this->em->getMetadataFactory()->isTransient($this->getClassName($class));
        } catch (\ReflectionException $e) {
            return false;
        }
    }

    protected function getClassName($class)
    {
        if (is_object($class)) {
            $class = ($class instanceof Proxy) ? get_parent_class($class) : get_class($class);
        }

        return $class;
    }
}
