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

namespace PSX\Validate;

use PSX\Data\GraphTraverser;
use PSX\Data\Record;
use PSX\Data\Visitor\ValidationVisitor;

/**
 * ValidatorAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class ValidatorAbstract implements ValidatorInterface
{
    protected $validate;
    protected $fields;
    protected $flag;

    protected $errors  = array();
    protected $visited = array();

    /**
     * @param \PSX\Validate\Validate $validate
     * @param \PSX\Validate\Property[] $fields
     * @param integer $flag
     */
    public function __construct(Validate $validate, array $fields = null, $flag = self::THROW_ERRORS)
    {
        $this->validate = $validate;
        $this->fields   = $fields;
        $this->flag     = $flag;
    }

    /**
     * @param \PSX\Validate\Property[] $fields
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return \PSX\Validate\Property[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param integer $flag
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;
    }

    /**
     * If the flag COLLECT_ERRORS is set this method returns all errors which
     * occured
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Clears the internal state of the validator
     */
    public function clear()
    {
        $this->errors  = [];
        $this->visited = [];
    }

    /**
     * Returns all fields which are required but not visited
     *
     * @return array
     */
    public function getRequiredNames()
    {
        $fields = $this->getFields();
        $result = array();

        foreach ($fields as $property) {
            if (!in_array($property->getName(), $this->visited)) {
                $result[] = $property->getName();
            }
        }

        return $result;
    }

    /**
     * Returns whether we have errors collected or not
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        return count($this->errors) == 0;
    }

    public function validate($data)
    {
        $this->clear();

        $traverser = new GraphTraverser();
        $visitor   = new ValidationVisitor($this);

        $traverser->traverse($data, $visitor);
    }

    public function validateProperty($path, $data)
    {
        $property = $this->getProperty($path);

        if ($property instanceof Property) {
            if (!in_array($property->getName(), $this->visited)) {
                $this->visited[] = $property->getName();
            }

            $this->validateValue($property, $data, $path);
        }
    }

    /**
     * Returns the validated value or throws an exception. If the flag
     * COLLECT_ERRORS was set null gets returned on an invalid value
     *
     * @param \PSX\Validate\Property $property
     * @param mixed $value
     * @param string $key
     * @return mixed
     */
    protected function validateValue(Property $property, $value, $key)
    {
        try {
            $result = $this->validate->apply($value, $property->getType(), $property->getFilters(), $key, $property->isRequired());

            // if we have no error and the value is not true the filter has
            // modified the value
            if ($result !== true) {
                return $result;
            } else {
                return $value;
            }
        } catch (ValidationException $e) {
            if ($this->flag == self::COLLECT_ERRORS) {
                $this->errors[$property->getName()] = $e->getResult();

                return null;
            } else {
                throw $e;
            }
        }
    }

    /**
     * Returns the property defined by the name
     *
     * @param string $name
     * @return \PSX\Validate\Property|null
     */
    protected function getProperty($name)
    {
        $name = ltrim($name, '/');

        foreach ($this->fields as $property) {
            if (preg_match('#^' . $name . '$#', ltrim($property->getName(), '/'))) {
                return $property;
            }
        }

        return null;
    }

    /**
     * Returns all available property names
     *
     * @return array
     */
    protected function getPropertyNames()
    {
        $fields = array();

        foreach ($this->fields as $property) {
            $fields[] = $property->getName();
        }

        return $fields;
    }
}
