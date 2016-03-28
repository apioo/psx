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

namespace PSX\Framework\Tests\Exception;

use PSX\Framework\DisplayException;
use PSX\Framework\Exception\Converter;
use PSX\Framework\Template\ErrorException;
use RuntimeException;

/**
 * ConverterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertDebug()
    {
        $converter = new Converter(true);
        $record    = $converter->convert($this->getRuntimeException());

        $this->assertEquals(false, $record->getSuccess());
        $this->assertEquals('RuntimeException', $record->getTitle());
        $this->assertTrue(strpos($record->getMessage(), 'foo') !== false);
        $this->assertTrue(strpos($record->getTrace(), 'PSX\Framework\Tests\Exception\ConverterTest->testConvertDebug()') !== false);
        $this->assertTrue(strpos($record->getContext(), '// [CONTEXT-START]') !== false);
        $this->assertTrue(strpos($record->getContext(), '// [CONTEXT-END]') !== false);
    }

    public function testConvertDebugErrorException()
    {
        $converter = new Converter(true);

        try {
            throw new ErrorException('foo', $this->getRuntimeException(), '/foo.php', '<b>bar</b>');
        } catch (ErrorException $e) {
            $record = $converter->convert($e);

            $this->assertEquals(false, $record->getSuccess());
            $this->assertEquals('RuntimeException', $record->getTitle());
            $this->assertTrue(strpos($record->getMessage(), 'foo') !== false);
            $this->assertTrue(strpos($record->getTrace(), 'PSX\Framework\Tests\Exception\ConverterTest->testConvertDebugErrorException()') !== false);
            $this->assertTrue(strpos($record->getContext(), '// [CONTEXT-START]') !== false);
            $this->assertTrue(strpos($record->getContext(), '// [CONTEXT-END]') !== false);
        }
    }

    public function testConvertLive()
    {
        $converter = new Converter(false);
        $record    = $converter->convert($this->getRuntimeException());

        $this->assertEquals(false, $record->getSuccess());
        $this->assertEquals('Internal Server Error', $record->getTitle());
        $this->assertEquals('The server encountered an internal error and was unable to complete your request.', $record->getMessage());
        $this->assertEquals('', $record->getTrace());
        $this->assertEquals('', $record->getContext());
    }

    public function testConvertLiveDisplayException()
    {
        $converter = new Converter(false);

        try {
            throw new DisplayException('foo');
        } catch (DisplayException $e) {
            $record = $converter->convert($e);

            $this->assertEquals(false, $record->getSuccess());
            $this->assertEquals('Internal Server Error', $record->getTitle());
            $this->assertEquals('foo', $record->getMessage());
            $this->assertEquals('', $record->getTrace());
            $this->assertEquals('', $record->getContext());
        }
    }

    protected function getRuntimeException()
    {
        try {
            // [CONTEXT-START]



            throw new RuntimeException('foo');



            // [CONTEXT-END]
        } catch (RuntimeException $e) {
            return $e;
        }

        return null;
    }
}
