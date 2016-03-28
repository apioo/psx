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

namespace PSX\Framework\Util\Api;

use PSX\DateTime\DateTime;
use PSX\Sql\Condition;
use PSX\Sql\Sql;

/**
 * FilterParameter
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class FilterParameter
{
    protected $fields;
    protected $startIndex;
    protected $count;
    protected $sortBy;
    protected $sortOrder;
    protected $filterBy;
    protected $filterOp;
    protected $filterValue;
    protected $updatedSince;

    public function setFields(array $fields = null)
    {
        $this->fields = $fields;
    }
    
    public function getFields()
    {
        return $this->fields;
    }

    public function setStartIndex($startIndex)
    {
        $this->startIndex = $startIndex;
    }
    
    public function getStartIndex()
    {
        return $this->startIndex;
    }

    public function setCount($count)
    {
        $this->count = $count;
    }
    
    public function getCount()
    {
        return $this->count;
    }

    public function setSortBy($sortBy)
    {
        $this->sortBy = $sortBy;
    }
    
    public function getSortBy()
    {
        return $this->sortBy;
    }

    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }
    
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    public function setFilterBy($filterBy)
    {
        $this->filterBy = $filterBy;
    }
    
    public function getFilterBy()
    {
        return $this->filterBy;
    }

    public function setFilterOp($filterOp)
    {
        $this->filterOp = $filterOp;
    }
    
    public function getFilterOp()
    {
        return $this->filterOp;
    }

    public function setFilterValue($filterValue)
    {
        $this->filterValue = $filterValue;
    }
    
    public function getFilterValue()
    {
        return $this->filterValue;
    }

    public function setUpdatedSince($updatedSince)
    {
        $this->updatedSince = $updatedSince;
    }
    
    public function getUpdatedSince()
    {
        return $this->updatedSince;
    }

    public static function extract(array $parameters)
    {
        $filter = new self();

        $fields       = isset($parameters['fields']) ? $parameters['fields'] : null;
        $startIndex   = isset($parameters['startIndex']) ? $parameters['startIndex'] : null;
        $count        = isset($parameters['count']) ? $parameters['count'] : null;
        $sortBy       = isset($parameters['sortBy']) ? $parameters['sortBy'] : null;
        $sortOrder    = isset($parameters['sortOrder']) ? $parameters['sortOrder'] : null;
        $filterBy     = isset($parameters['filterBy']) ? $parameters['filterBy'] : null;
        $filterOp     = isset($parameters['filterOp']) ? $parameters['filterOp'] : null;
        $filterValue  = isset($parameters['filterValue']) ? $parameters['filterValue'] : null;
        $updatedSince = isset($parameters['updatedSince']) ? $parameters['updatedSince'] : null;

        if (!empty($fields)) {
            $parts  = explode(',', $fields);
            $fields = array();

            foreach ($parts as $field) {
                $field = trim($field);

                if (strlen($field) > 0 && strlen($field) < 32 && ctype_alnum($field)) {
                    $fields[] = $field;
                }
            }
        } else {
            $fields = null;
        }

        $filter->setFields($fields);

        $startIndex = (int) $startIndex;
        if (!empty($startIndex) && $startIndex > 0) {
            $filter->setStartIndex($startIndex);
        }

        $count = (int) $count;
        if (!empty($count) && $count > 0) {
            $filter->setCount($count);
        }

        if (!empty($sortBy) && strlen($sortBy) < 128) {
            $filter->setSortBy($sortBy);
        }

        if (!empty($sortOrder)) {
            switch (strtolower($sortOrder)) {
                case 'asc':
                case 'ascending':
                    $filter->setSortOrder(Sql::SORT_ASC);
                    break;

                case 'desc':
                case 'descending':
                    $filter->setSortOrder(Sql::SORT_DESC);
                    break;
            }
        }

        if (!empty($filterBy) && ctype_alnum($filterBy) && strlen($filterBy) < 32) {
            $filter->setFilterBy($filterBy);
        }

        if (!empty($filterOp) && in_array($filterOp, array('contains', 'equals', 'startsWith', 'present'))) {
            $filter->setFilterOp($filterOp);
        }

        if (!empty($filterValue) && strlen($filterValue) < 128) {
            $filter->setFilterValue($filterValue);
        }

        if (!empty($updatedSince)) {
            $filter->setUpdatedSince(new \DateTime($updatedSince));
        }

        return $filter;
    }

    public static function getCondition(FilterParameter $parameter, $dateColumn = 'date')
    {
        $condition = new Condition();

        if ($parameter->getFilterBy() && $parameter->getFilterValue()) {
            switch ($parameter->getFilterOp()) {
                case 'contains':
                    $condition->like($parameter->getFilterBy(), '%' . $parameter->getFilterValue() . '%');
                    break;

                case 'equals':
                    $condition->equals($parameter->getFilterBy(), $parameter->getFilterValue());
                    break;

                case 'startsWith':
                    $condition->like($parameter->getFilterBy(), $parameter->getFilterValue() . '%');
                    break;

                case 'present':
                    $condition->notNil($parameter->getFilterBy());
                    $condition->notEquals($parameter->getFilterBy(), '');
                    break;
            }
        }

        if ($parameter->getUpdatedSince() instanceof \DateTime) {
            $condition->greater($dateColumn, $parameter->getUpdatedSince());
        }

        return $condition;
    }
}
