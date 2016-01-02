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

namespace PSX\Data;

use PSX\Filter;
use PSX\Validate;

/**
 * AccessorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AccessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideSources
     */
    public function testGet($source)
    {
        $accessor = new Accessor(new Validate(), $source);

        $this->assertEquals($source, $accessor->getSource());
        $this->assertEquals('bar', $accessor->get('/foo'));
        $this->assertEquals(1, $accessor->get('/bar/foo'));
        $this->assertEquals('bar', $accessor->get('/tes/0/foo'));
    }

    /**
     * @expectedException \PSX\Validate\ValidationException
     * @expectedExceptionMessage bar is not set
     */
    public function testGetMissing()
    {
        $accessor = new Accessor(new Validate(), ['foo' => 'bar']);
        $accessor->get('bar');
    }

    /**
     * @expectedException \PSX\Validate\ValidationException
     * @expectedExceptionMessage lorem is not set
     */
    public function testGetMissingWithTitle()
    {
        $accessor = new Accessor(new Validate(), ['foo' => 'bar']);
        $accessor->get('bar', Validate::TYPE_STRING, [], 'lorem');
    }

    public function testGetNotRequired()
    {
        $accessor = new Accessor(new Validate(), ['foo' => 'bar']);

        $this->assertNull($accessor->get('bar', Validate::TYPE_STRING, [], null, false));
    }

    /**
     * @dataProvider provideSources
     */
    public function testGetFilter($source)
    {
        $filter = new Filter\Length(3, 8);

        $validate = $this->getMockBuilder('PSX\Validate')
            ->setMethods(array('apply'))
            ->getMock();

        $validate->expects($this->at(0))
            ->method('apply')
            ->with($this->equalTo('bar'), $this->equalTo(Validate::TYPE_STRING), $this->equalTo(array()));

        $validate->expects($this->at(1))
            ->method('apply')
            ->with($this->equalTo(1), $this->equalTo(Validate::TYPE_INTEGER), $this->equalTo(array()));

        $validate->expects($this->at(2))
            ->method('apply')
            ->with($this->equalTo('bar'), $this->equalTo(Validate::TYPE_STRING), $this->equalTo(array($filter)));

        $accessor = new Accessor($validate, $source);

        $accessor->get('/foo');
        $accessor->get('/bar/foo', Validate::TYPE_INTEGER);
        $accessor->get('/tes/0/foo', Validate::TYPE_STRING, array($filter));
    }

    public function provideSources()
    {
        $sources = array();

        // array
        $source = array(
            'foo' => 'bar',
            'bar' => array(
                'foo' => '1',
            ),
            'tes' => array(
                array(
                    'foo' => 'bar'
                ),
            ),
        );

        $sources[] = [$source];

        // stdClass
        $source = new \stdClass();
        $source->foo = 'bar';
        $source->bar = new \stdClass();
        $source->bar->foo = 1;
        $source->tes = array();
        $source->tes[0] = new \stdClass();
        $source->tes[0]->foo = 'bar';

        $sources[] = [$source];

        // RecordInterface
        $source = Record::fromArray([
            'foo' => 'bar',
            'bar' => Record::fromArray([
                'foo' => '1'
            ]),
            'tes' => [
                Record::fromArray([
                    'foo' => 'bar'
                ])
            ]
        ]);

        $sources[] = [$source];

        return $sources;
    }

    /**
     * @expectedException \PSX\Validate\ValidationException
     */
    public function testGetUnknownKey()
    {
        $source = array(
            'bar' => array(
                'foo' => '1',
            ),
        );

        $accessor = new Accessor(new Validate(), $source);
        $accessor->get('/bar/bar');
    }

    /**
     * @expectedException \PSX\Validate\ValidationException
     */
    public function testGetUnknownKeyInvalidSource()
    {
        $source = 'foo';

        $accessor = new Accessor(new Validate(), $source);
        $accessor->get('/bar/bar');
    }
}
