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

namespace PSX\Framework\Tests\Controller\Foo\Application;

use PSX\Data\RecordObject;
use PSX\Framework\Controller\ApiAbstract;
use PSX\Data\Record;
use PSX\Data\RecordAbstract;
use PSX\Sql\Sql;
use PSX\Framework\Util\Api\FilterParameter;

/**
 * TestApiController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestApiController extends ApiAbstract
{
    /**
     * @Inject
     * @var \PHPUnit_Framework_TestCase
     */
    protected $testCase;

    public function doIndex()
    {
        $record = new Record('foo', array('bar' => 'foo'));

        $this->setBody($record);
    }

    public function doInsert()
    {
        $record = $this->getBodyAs(NewsRecord::class);

        $this->setBody($record);
    }

    public function doInspect()
    {
        $params = FilterParameter::extract($this->getParameters());

        $this->testCase->assertEquals(array('foo', 'bar'), $params->getFields());
        $this->testCase->assertEquals('2014-01-26', $params->getUpdatedSince()->format('Y-m-d'));
        $this->testCase->assertEquals(8, $params->getCount());
        $this->testCase->assertEquals('id', $params->getFilterBy());
        $this->testCase->assertEquals('equals', $params->getFilterOp());
        $this->testCase->assertEquals('12', $params->getFilterValue());
        $this->testCase->assertEquals('id', $params->getSortBy());
        $this->testCase->assertEquals(Sql::SORT_DESC, $params->getSortOrder());
        $this->testCase->assertEquals(4, $params->getStartIndex());

        $condition = FilterParameter::getCondition($params);

        $this->testCase->assertEquals('WHERE (id = ? AND date > ?)', $condition->getStatment());
        $this->testCase->assertEquals(['12', '2014-01-26 00:00:00'], $condition->getValues());
    }
}

class NewsRecord extends RecordObject
{
    protected $title;
    protected $user;

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
    
    public function getUser()
    {
        return $this->user;
    }
}
