<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Controller\SchemaApi;

use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Data\Writer;
use PSX\DateTime;
use PSX\DateTime\Date;
use PSX\DateTime\Duration;
use PSX\DateTime\Time;
use PSX\Json;
use PSX\Test\ControllerTestCase;

/**
 * PropertyTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class PropertyTestCase extends ControllerTestCase
{
    /**
     * @dataProvider getDataTypes
     */
    public function testGet($type)
    {
        $response = $this->sendRequest('http://127.0.0.1/api?type=' . $type, 'GET');
        $body     = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString(self::getExpected(), $body, $body);
    }

    public function testPost()
    {
        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', [], self::getExpected());
        $body     = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString(self::getExpected(), $body, $body);
    }

    public function testPostInvalidAny()
    {
        $data = <<<JSON
{
    "any": {
        "foo": {
        }
    }
}
JSON;

        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/any/foo must be a string', substr($data->message, 0, 25), $body);
    }

    public function testPostInvalidArray()
    {
        $data = <<<JSON
{
    "array": "foo"
}
JSON;

        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/array must be an array', substr($data->message, 0, 23), $body);
    }

    public function testPostInvalidArrayComplex()
    {
        $data = <<<JSON
{
    "arrayComplex": "foo"
}
JSON;

        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/arrayComplex must be an array', substr($data->message, 0, 30), $body);
    }

    public function testPostInvalidArrayChoice()
    {
        $data = <<<JSON
{
    "arrayChoice": [{
        "foo": "baz"
    },{
        "baz": "bar"
    },{
        "foo": "foo"
    }]
}
JSON;

        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/arrayChoice/1 must be one of the following objects [a, b]', substr($data->message, 0, 58), $body);
    }

    public function testPostInvalidBoolean()
    {
        $data = <<<JSON
{
    "boolean": "foo"
}
JSON;

        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/boolean must be boolean', substr($data->message, 0, 24), $body);
    }

    public function testPostInvalidChoice()
    {
        $data = <<<JSON
{
    "choice": {
        "baz": "test"
    }
}
JSON;

        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/choice must be one of the following objects [a, b]', substr($data->message, 0, 51), $body);
    }

    public function testPostInvalidComplex()
    {
        $data = <<<JSON
{
    "complex": {
        "baz": "test"
    }
}
JSON;

        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/complex property "baz" does not exist', substr($data->message, 0, 38), $body);
    }

    public function testPostInvalidDateTime()
    {
        $data = <<<JSON
{
    "dateTime": "foo"
}
JSON;

        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/dateTime must be an valid date-time format (full-date "T" full-time) [RFC3339]', substr($data->message, 0, 79), $body);
    }

    public function testPostInvalidDate()
    {
        $data = <<<JSON
{
    "date": "foo"
}
JSON;

        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/date must be an valid full-date format (date-fullyear "-" date-month "-" date-mday) [RFC3339]', substr($data->message, 0, 94), $body);
    }

    public function testPostInvalidDuration()
    {
        $data = <<<JSON
{
    "duration": "foo"
}
JSON;

        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/duration must be an valid duration format [ISO8601]', substr($data->message, 0, 52), $body);
    }

    public function testPostInvalidFloat()
    {
        $data = <<<JSON
{
    "float": "foo"
}
JSON;

        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/float must be an float', substr($data->message, 0, 23), $body);
    }

    public function testPostInvalidInteger()
    {
        $data = <<<JSON
{
    "integer": "foo"
}
JSON;

        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/integer must be an integer', substr($data->message, 0, 27), $body);
    }

    public function testPostInvalidString()
    {
        $data = <<<JSON
{
    "string": []
}
JSON;

        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/string must be a string', substr($data->message, 0, 24), $body);
    }

    public function testPostInvalidTime()
    {
        $data = <<<JSON
{
    "time": "foo"
}
JSON;

        $response = $this->sendRequest('http://127.0.0.1/api', 'POST', [], $data);
        $body     = (string) $response->getBody();
        $data     = json_decode($body);

        $this->assertEquals(500, $response->getStatusCode(), $body);
        $this->assertEquals('/time must be an valid full-time format (partial-time time-offset) [RFC3339]', substr($data->message, 0, 76), $body);
    }

    /**
     * Checks whether the data we received as post is converted to the right
     * types
     *
     * @param \PHPUnit_Framework_TestCase $testCase
     * @param \PSX\Data\RecordInterface $record
     */
    public static function assertRecord(\PHPUnit_Framework_TestCase $testCase, RecordInterface $record)
    {
        $testCase->assertInstanceOf('PSX\Data\RecordInterface', $record->getAny());
        $testCase->assertEquals(['foo' => 'bar'], $record->getAny()->getRecordInfo()->getFields());
        $testCase->assertInternalType('array', $record->getArray());
        $testCase->assertEquals(1, count($record->getArray()));
        $testCase->assertEquals(['bar'], $record->getArray());
        $testCase->assertInternalType('array', $record->getArrayComplex());
        $testCase->assertEquals(2, count($record->getArrayComplex()));
        $testCase->assertInstanceOf('PSX\Data\RecordInterface', $record->getArrayComplex()[0]);
        $testCase->assertEquals(['foo' => 'bar'], $record->getArrayComplex()[0]->getRecordInfo()->getFields());
        $testCase->assertInstanceOf('PSX\Data\RecordInterface', $record->getArrayComplex()[1]);
        $testCase->assertEquals(['foo' => 'foo'], $record->getArrayComplex()[1]->getRecordInfo()->getFields());
        $testCase->assertInternalType('array', $record->getArrayChoice());
        $testCase->assertEquals(3, count($record->getArrayChoice()));
        $testCase->assertInstanceOf('PSX\Data\RecordInterface', $record->getArrayChoice()[0]);
        $testCase->assertEquals(['foo' => 'baz'], $record->getArrayChoice()[0]->getRecordInfo()->getFields());
        $testCase->assertInstanceOf('PSX\Data\RecordInterface', $record->getArrayChoice()[1]);
        $testCase->assertEquals(['bar' => 'bar'], $record->getArrayChoice()[1]->getRecordInfo()->getFields());
        $testCase->assertInstanceOf('PSX\Data\RecordInterface', $record->getArrayChoice()[2]);
        $testCase->assertEquals(['foo' => 'foo'], $record->getArrayChoice()[2]->getRecordInfo()->getFields());
        $testCase->assertInternalType('boolean', $record->getBoolean());
        $testCase->assertEquals(true, $record->getBoolean());
        $testCase->assertInstanceOf('PSX\Data\RecordInterface', $record->getChoice());
        $testCase->assertEquals(['foo' => 'bar'], $record->getComplex()->getRecordInfo()->getFields());
        $testCase->assertInstanceOf('PSX\Data\RecordInterface', $record->getComplex());
        $testCase->assertEquals(['foo' => 'bar'], $record->getComplex()->getRecordInfo()->getFields());
        $testCase->assertInstanceOf('PSX\DateTime\Date', $record->getDate());
        $testCase->assertEquals('2015-05-01', $record->getDate()->format('Y-m-d'));
        $testCase->assertInstanceOf('PSX\DateTime', $record->getDateTime());
        $testCase->assertEquals('2015-05-01T13:37:14Z', $record->getDateTime()->format('Y-m-d\TH:i:s\Z'));
        $testCase->assertInstanceOf('PSX\DateTime\Duration', $record->getDuration());
        $testCase->assertEquals('000100000000', $record->getDuration()->format('%Y%M%D%H%I%S'));
        $testCase->assertInternalType('float', $record->getFloat());
        $testCase->assertEquals(13.37, $record->getFloat());
        $testCase->assertInternalType('integer', $record->getInteger());
        $testCase->assertEquals(7, $record->getInteger());
        $testCase->assertInternalType('string', $record->getString());
        $testCase->assertEquals('bar', $record->getString());
        $testCase->assertInstanceOf('PSX\DateTime\Time', $record->getTime());
        $testCase->assertEquals('13:37:14', $record->getTime()->format('H:i:s'));
    }

    /**
     * Returns all available data types which can be used as data provider
     *
     * @return array
     */
    public static function getDataTypes()
    {
        return [
            [1],
            [2],
            [3],
        ];
    }

    /**
     * Returns different responses. The assimilator should convert all these
     * types to the same response format
     *
     * @param integer $type
     * @return array
     */
    public static function getDataByType($type)
    {
        switch ($type) {
            case 1:
                // we return actual types
                return array(
                    'any' => [
                        'foo' => 'bar'
                    ],
                    'array' => ['bar'],
                    'arrayComplex' => [[
                        'foo' => 'bar'
                    ],[
                        'foo' => 'foo'
                    ]],
                    'arrayChoice' => [[
                        'foo' => 'baz'
                    ],[
                        'bar' => 'bar'
                    ],[
                        'foo' => 'foo'
                    ]],
                    'boolean' => true,
                    'choice' => [
                        'bar' => 'test'
                    ],
                    'complex' => [
                        'foo' => 'bar'
                    ],
                    'date' => new Date(2015, 5, 1),
                    'dateTime' => new DateTime(2015, 5, 1, 13, 37, 14),
                    'duration' => new Duration('P1M'),
                    'float' => 13.37,
                    'integer' => 7,
                    'string' => 'bar',
                    'time' => new Time(13, 37, 14),
                );
                break;

            case 2:
                // we return only strings like from an database
                return array(
                    'any' => [
                        'foo' => 'bar'
                    ],
                    'array' => ['bar'],
                    'arrayComplex' => [[
                        'foo' => 'bar'
                    ],[
                        'foo' => 'foo'
                    ]],
                    'arrayChoice' => [[
                        'foo' => 'baz'
                    ],[
                        'bar' => 'bar'
                    ],[
                        'foo' => 'foo'
                    ]],
                    'boolean' => 'true',
                    'choice' => [
                        'bar' => 'test'
                    ],
                    'complex' => [
                        'foo' => 'bar'
                    ],
                    'date' => '2015-05-01',
                    'dateTime' => '2015-05-01T13:37:14Z',
                    'duration' => 'P1M',
                    'float' => '13.37',
                    'integer' => '7',
                    'string' => 'bar',
                    'time' => '13:37:14',
                );
                break;

            case 3:
                // we return types which we get from the doctrine mapper
                return array(
                    'any' => [
                        'foo' => 'bar'
                    ],
                    'array' => ['bar'],
                    'arrayComplex' => [[
                        'foo' => 'bar'
                    ],[
                        'foo' => 'foo'
                    ]],
                    'arrayChoice' => [[
                        'foo' => 'baz'
                    ],[
                        'bar' => 'bar'
                    ],[
                        'foo' => 'foo'
                    ]],
                    'boolean' => true,
                    'choice' => [
                        'bar' => 'test'
                    ],
                    'complex' => [
                        'foo' => 'bar'
                    ],
                    'date' => new \DateTime('2015-05-01T13:37:14Z'),
                    'dateTime' => new \DateTime('2015-05-01T13:37:14Z'),
                    'duration' => 'P1M',
                    'float' => 13.37,
                    'integer' => 7,
                    'string' => 'bar',
                    'time' => new \DateTime('2015-05-01T13:37:14Z'),
                );
                break;
        }
    }

    /**
     * The JSON format which we expect as response
     *
     * @return string
     */
    public static function getExpected()
    {
        return <<<JSON
{
    "any": {
        "foo": "bar"
    },
    "array": [
        "bar"
    ],
    "arrayComplex": [{
        "foo": "bar"
    },{
        "foo": "foo"
    }],
    "arrayChoice": [{
        "foo": "baz"
    },{
        "bar": "bar"
    },{
        "foo": "foo"
    }],
    "boolean": true,
    "choice": {
        "bar": "test"
    },
    "complex": {
        "foo": "bar"
    },
    "date": "2015-05-01",
    "dateTime": "2015-05-01T13:37:14Z",
    "duration": "P1M",
    "float": 13.37,
    "integer": 7,
    "string": "bar",
    "time": "13:37:14"
}
JSON;
    }
}
