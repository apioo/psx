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

namespace PSX\Util;

/**
 * CurveArrayTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CurveArrayTest extends \PHPUnit_Framework_TestCase
{
    protected $nestArray;
    protected $flattenArray;

    protected function setUp()
    {
        $this->nestArray = new \stdClass();
        $this->nestArray->id = null;
        $this->nestArray->title = null;
        $this->nestArray->author = new \stdClass();
        $this->nestArray->author->name = null;
        $this->nestArray->author->title = null;
        $this->nestArray->author->group = new \stdClass();
        $this->nestArray->author->group->id = null;
        $this->nestArray->author->group->name = null;
        $this->nestArray->content = null;
        $this->nestArray->date = null;

        $this->flattenArray = array(
            'id' => null,
            'title' => null,
            'author_name' => null,
            'author_title' => null,
            'author_group_id' => null,
            'author_group_name' => null,
            'content' => null,
            'date' => null,
        );
    }

    public function testNest()
    {
        $this->assertEquals($this->nestArray, CurveArray::nest($this->flattenArray));
    }

    public function testNestArray()
    {
        $this->assertEquals([$this->nestArray, $this->nestArray], CurveArray::nest([$this->flattenArray, $this->flattenArray]));
    }

    public function testFlatten()
    {
        $this->assertEquals($this->flattenArray, CurveArray::flatten($this->nestArray));
    }

    public function testFlattenAssocArray()
    {
        $this->assertEquals($this->flattenArray, CurveArray::flatten(array(
            'id' => null,
            'title' => null,
            'author' => array(
                'name' => null,
                'title' => null,
                'group' => array(
                    'id' => null,
                    'name' => null,
                ),
            ),
            'content' => null,
            'date' => null,
        )));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFlattenInvalidData()
    {
        CurveArray::flatten('foo');
    }

    public function testInverseNest()
    {
        $this->assertEquals($this->nestArray, CurveArray::nest(CurveArray::flatten($this->nestArray)));
    }

    public function testInverseFlatten()
    {
        $this->assertEquals($this->flattenArray, CurveArray::flatten(CurveArray::nest($this->flattenArray)));
    }

    public function testIsAssoc()
    {
        $this->assertFalse(CurveArray::isAssoc(array()));
        $this->assertFalse(CurveArray::isAssoc(range(0, 9)));
        $this->assertTrue(CurveArray::isAssoc(array('foo' => null, 'bar' => null)));
        $this->assertTrue(CurveArray::isAssoc(array(0 => 'foo', 'foo' => null, 'bar' => null)));
    }

    public function testObjectify()
    {
        $data = array(
            'foo' => 'bar',
            'bar' => array(
                'test' => 1,
                'foo' => 2,
            ),
            'tags' => array('foo', 'bar')
        );

        $expect = new \stdClass();
        $expect->foo = 'bar';
        $expect->bar = new \stdClass();
        $expect->bar->test = 1;
        $expect->bar->foo = 2;
        $expect->tags = array('foo', 'bar');

        $this->assertEquals($expect, CurveArray::objectify($data));
    }
}
