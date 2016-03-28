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

namespace PSX\Data\Tests;

use PSX\Exception;

/**
 * ResultSetTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResultSetTest extends \PHPUnit_Framework_TestCase
{
    public function testFullResultSet()
    {
        $entries = array(
            array('id' => 1, 'title' => 'foo'),
            array('id' => 2, 'title' => 'bar'),
            array('id' => 3, 'title' => 'blu'),
            array('id' => 4, 'title' => 'bla'),
        );

        $resultSet = new ResultSet(12, 0, 2, $entries);

        $this->assertEquals(4, count($resultSet));
        $this->assertEquals(4, $resultSet->count());
        $this->assertEquals(12, $resultSet->getTotalResults());
        $this->assertEquals(0, $resultSet->getStartIndex());
        $this->assertEquals(2, $resultSet->getItemsPerPage());
        $this->assertEquals(false, $resultSet->isEmpty());

        foreach ($resultSet as $i => $result) {
            $this->assertEquals($i + 1, $result['id']);
        }

        // test internal reset
        foreach ($resultSet as $i => $result) {
            $this->assertEquals($i + 1, $result['id']);
        }
    }

    public function testEmptyResultSet()
    {
        $resultSet = new ResultSet(12, 0, 2, array());

        $this->assertEquals(0, count($resultSet));
        $this->assertEquals(0, $resultSet->count());
        $this->assertEquals(true, $resultSet->isEmpty());

        foreach ($resultSet as $row) {
            throw new Exception('Should not happen');
        }
    }
}
