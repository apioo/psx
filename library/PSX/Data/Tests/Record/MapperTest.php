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

namespace PSX\Data\Tests\Record;

use PSX\Data\Record;
use PSX\Data\Record\Mapper;
use PSX\Data\Record\Mapper\Rule;

/**
 * MapperTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class MapperTest extends \PHPUnit_Framework_TestCase
{
    public function testMap()
    {
        $source = Record::fromArray([
            'id' => 1,
            // userId is not available in the source
            'userId' => 12,
            // underscore names are converted to camelcase
            'right_level' => 'bar',
            'title' => 'foo',
            'content' => 'bar',
            'rating' => 'a-rating',
            'date' => '2014-09-06',
        ]);

        $destination = new Destination();
        $testCase    = $this;

        Mapper::map($source, $destination, array(
            'title'   => 'description',
            'content' => new Rule('content'),
            'rating'  => new Rule('level', 'no-rating'),
            'date'    => new Rule('date', function ($value) use ($testCase) {
                $testCase->assertEquals('2014-09-06', $value);

                return strtotime($value);
            }),
        ));

        $this->assertEquals($source->id, $destination->getId());
        $this->assertEquals($source->right_level, $destination->getRightLevel());
        $this->assertEquals($source->title, $destination->getDescription());
        $this->assertEquals($source->content, $destination->getContent());
        $this->assertEquals('no-rating', $destination->getLevel());
        $this->assertEquals('1409961600', $destination->getDate());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMapInvalidDestination()
    {
        Mapper::map(new Record(), 'foo', array());
    }
}

class Destination
{
    protected $id;
    protected $rightLevel;
    protected $description;
    protected $content;
    protected $level;
    protected $date;

    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setRightLevel($rightLevel)
    {
        $this->rightLevel = $rightLevel;
    }
    
    public function getRightLevel()
    {
        return $this->rightLevel;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    public function getDescription()
    {
        return $this->description;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }
    
    public function getContent()
    {
        return $this->content;
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }
    
    public function getLevel()
    {
        return $this->level;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }
    
    public function getDate()
    {
        return $this->date;
    }
}
