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

namespace PSX\Framework\Tests\Util\Api;

use DateTime;
use PSX\Framework\Util\Api\FilterParameter;
use PSX\Sql\Sql;

/**
 * FilterParameterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class FilterParameterTest extends \PHPUnit_Framework_TestCase
{
    public function testExtract()
    {
        $fields       = isset($parameters['fields']) ? $parameters['fields'] : null;
        $startIndex   = isset($parameters['startIndex']) ? $parameters['startIndex'] : null;
        $count        = isset($parameters['count']) ? $parameters['count'] : null;
        $sortBy       = isset($parameters['sortBy']) ? $parameters['sortBy'] : null;
        $sortOrder    = isset($parameters['sortOrder']) ? $parameters['sortOrder'] : null;
        $filterBy     = isset($parameters['filterBy']) ? $parameters['filterBy'] : null;
        $filterOp     = isset($parameters['filterOp']) ? $parameters['filterOp'] : null;
        $filterValue  = isset($parameters['filterValue']) ? $parameters['filterValue'] : null;
        $updatedSince = isset($parameters['updatedSince']) ? $parameters['updatedSince'] : null;

        $filter = FilterParameter::extract([
            'fields'       => 'foo,bar',
            'startIndex'   => '2',
            'count'        => '8',
            'sortBy'       => 'id',
            'sortOrder'    => 'desc',
            'filterBy'     => 'title',
            'filterOp'     => 'equals',
            'filterValue'  => 'foo',
            'updatedSince' => '2015-08-19T18:44:04+02:00',
        ]);

        $this->assertEquals(['foo', 'bar'], $filter->getFields());
        $this->assertEquals(2, $filter->getStartIndex());
        $this->assertEquals(8, $filter->getCount());
        $this->assertEquals('id', $filter->getSortBy());
        $this->assertEquals(Sql::SORT_DESC, $filter->getSortOrder());
        $this->assertEquals('title', $filter->getFilterBy());
        $this->assertEquals('equals', $filter->getFilterOp());
        $this->assertEquals('foo', $filter->getFilterValue());
        $this->assertInstanceOf('DateTime', $filter->getUpdatedSince());
        $this->assertEquals('2015-08-19T18:44:04+02:00', $filter->getUpdatedSince()->format(DateTime::RFC3339));
    }

    public function testGetConditionContains()
    {
        $filter = new FilterParameter();
        $filter->setFilterBy('foo');
        $filter->setFilterOp('contains');
        $filter->setFilterValue('bar');

        $condition = FilterParameter::getCondition($filter);

        $this->assertEquals('WHERE (foo LIKE ?)', $condition->getStatment());
        $this->assertEquals(['%bar%'], $condition->getValues());
    }

    public function testGetConditionEquals()
    {
        $filter = new FilterParameter();
        $filter->setFilterBy('foo');
        $filter->setFilterOp('equals');
        $filter->setFilterValue('bar');

        $condition = FilterParameter::getCondition($filter);

        $this->assertEquals('WHERE (foo = ?)', $condition->getStatment());
        $this->assertEquals(['bar'], $condition->getValues());
    }

    public function testGetConditionStartsWith()
    {
        $filter = new FilterParameter();
        $filter->setFilterBy('foo');
        $filter->setFilterOp('startsWith');
        $filter->setFilterValue('bar');

        $condition = FilterParameter::getCondition($filter);

        $this->assertEquals('WHERE (foo LIKE ?)', $condition->getStatment());
        $this->assertEquals(['bar%'], $condition->getValues());
    }

    public function testGetConditionPresent()
    {
        $filter = new FilterParameter();
        $filter->setFilterBy('foo');
        $filter->setFilterOp('present');
        $filter->setFilterValue('bar');

        $condition = FilterParameter::getCondition($filter);

        $this->assertEquals('WHERE (foo IS NOT NULL AND foo != ?)', $condition->getStatment());
        $this->assertEquals([''], $condition->getValues());
    }

    public function testGetConditionUpdatedSince()
    {
        $date   = new DateTime('2015-08-19T18:44:04+02:00');
        $filter = new FilterParameter();
        $filter->setUpdatedSince($date);

        $condition = FilterParameter::getCondition($filter);

        $this->assertEquals('WHERE (date > ?)', $condition->getStatment());
        $this->assertEquals(['2015-08-19 18:44:04'], $condition->getValues());
    }
}
