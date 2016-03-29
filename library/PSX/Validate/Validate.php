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

use InvalidArgumentException;

/**
 * This class offers methods to sanitize values that came from untrusted
 * sources
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Validate
{
    const TYPE_INTEGER = 'integer';
    const TYPE_STRING  = 'string';
    const TYPE_FLOAT   = 'float';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_ARRAY   = 'array';
    const TYPE_OBJECT  = 'object';

    /**
     * Applies filter on the given value and returns the value on success or
     * throws an exception if an error occured
     *
     * @param string $value
     * @param string $type
     * @param \PSX\Validate\FilterInterface[]|callable $filters
     * @param string $title
     * @param boolean $required
     * @return mixed
     */
    public function apply($value, $type = self::TYPE_STRING, array $filters = array(), $title = null, $required = true)
    {
        $result = $this->validate($value, $type, $filters, $title, $required);

        if ($result->hasError()) {
            throw new ValidationException($result->getFirstError(), $title, $result);
        } elseif ($result->isSuccessful()) {
            return $result->getValue();
        }

        return null;
    }

    /**
     * Applies the $filter array containing PSX\Validate\FilterInterface on the
     * $value. Returns a result object which contains the value and error
     * messages from the filter. If $required is set to true an error will be
     * added if the $value is null
     *
     * @param string $value
     * @param string $type
     * @param \PSX\Validate\FilterInterface[]|callable $filters
     * @param string $title
     * @param boolean $required
     * @return \PSX\Validate\Result
     */
    public function validate($value, $type = self::TYPE_STRING, array $filters = array(), $title = null, $required = true)
    {
        $result = new Result();

        if ($title === null) {
            $title = 'Unknown';
        }

        if ($value === null) {
            if ($required === true) {
                $result->addError(sprintf('%s is not set', $title));

                return $result;
            } elseif ($required === false) {
                return $result;
            }
        } else {
            $value = $this->transformType($value, $type);
        }

        foreach ($filters as $filter) {
            $error = null;

            if ($filter instanceof FilterInterface) {
                $return = $filter->apply($value);
                $error  = $filter->getErrorMessage();
            } elseif (is_callable($filter)) {
                $return = call_user_func_array($filter, array($value));
            } else {
                throw new InvalidArgumentException('Filter must be either a callable or instanceof PSX\Validate\FilterInterface');
            }

            if ($return === false) {
                if ($error === null) {
                    $error = '%s is not valid';
                }

                $result->addError(sprintf($error, $title));

                return $result;
            } elseif ($return === true) {
                // the filter returns true so the validation was successful
            } else {
                $value = $return;
            }
        }

        $result->setValue($value);

        return $result;
    }

    protected function transformType($value, $type)
    {
        switch ($type) {
            case self::TYPE_INTEGER:
                return (int) $value;

            case self::TYPE_STRING:
                return (string) $value;

            case self::TYPE_FLOAT:
                return (float) $value;

            case self::TYPE_BOOLEAN:
                return (bool) $value;

            case self::TYPE_ARRAY:
                return (array) $value;

            case self::TYPE_OBJECT:
                return (object) $value;

            default:
                return $value;
        }
    }
}
