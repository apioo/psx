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

namespace PSX\Framework\Tests\Log;

use PSX\Framework\Log\ErrorFormatter;

/**
 * ErrorFormatterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ErrorFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PSX\Framework\Log\ErrorFormatter
     */
    protected $formatter;

    protected function setUp()
    {
        $this->formatter = new ErrorFormatter();
    }

    protected function tearDown()
    {
        $this->formatter = null;
    }

    public function testFormatBasic()
    {
        $record = array(
            'channel'    => 'psx',
            'level_name' => 'INFO',
            'message'    => 'foo',
        );

        $this->assertEquals('psx.INFO: foo', $this->formatter->format($record));
    }

    public function testFormatPhpError()
    {
        $record = array(
            'channel'    => 'psx',
            'level_name' => 'INFO',
            'message'    => 'foo',
            'context'    => array(
                'severity' => E_WARNING,
            ),
        );

        $this->assertEquals('psx.INFO: PHP Warning: foo', $this->formatter->format($record));
    }

    public function testFormatFileLine()
    {
        $record = array(
            'channel'    => 'psx',
            'level_name' => 'INFO',
            'message'    => 'foo',
            'context'    => array(
                'file' => 'foo.php',
                'line' => 12,
            ),
        );

        $this->assertEquals('psx.INFO: foo in foo.php on line 12', $this->formatter->format($record));
    }

    public function testFormatTrace()
    {
        $record = array(
            'channel'    => 'psx',
            'level_name' => 'INFO',
            'message'    => 'foo',
            'context'    => array(
                'trace' => '#0 {main}',
            ),
        );

        $this->assertEquals('psx.INFO: foo' . "\n" . 'Stack trace:' . "\n" . '#0 {main}', $this->formatter->format($record));
    }

    public function testFormatFull()
    {
        $record = array(
            'channel'    => 'psx',
            'level_name' => 'INFO',
            'message'    => 'foo',
            'context'    => array(
                'file' => 'foo.php',
                'line' => 12,
                'trace' => '#0 {main}',
            ),
        );

        $this->assertEquals('psx.INFO: foo in foo.php on line 12' . "\n" . 'Stack trace:' . "\n" . '#0 {main}', $this->formatter->format($record));
    }

    /**
     * @dataProvider phpErrorProvider
     */
    public function testAllPhpErrors($level, $name)
    {
        $record = array(
            'channel'    => 'psx',
            'level_name' => 'INFO',
            'message'    => 'foo',
            'context'    => array(
                'severity' => $level,
            ),
        );

        $this->assertEquals('psx.INFO: ' . $name . ': foo', $this->formatter->format($record));
    }

    public function phpErrorProvider()
    {
        return array(
            [E_ERROR, 'PHP Error'],
            [E_CORE_ERROR, 'PHP Core error'],
            [E_COMPILE_ERROR, 'PHP Compile error'],
            [E_USER_ERROR, 'PHP User error'],
            [E_RECOVERABLE_ERROR, 'PHP Recoverable error'],
            [E_WARNING, 'PHP Warning'],
            [E_CORE_WARNING, 'PHP Core warning'],
            [E_COMPILE_WARNING, 'PHP Compile warning'],
            [E_USER_WARNING, 'PHP User warning'],
            [E_PARSE, 'PHP Parse'],
            [E_NOTICE, 'PHP Notice'],
            [E_USER_NOTICE, 'PHP User notice'],
            [E_STRICT, 'PHP Strict'],
            [E_DEPRECATED, 'PHP Deprecated'],
            [E_USER_DEPRECATED, 'PHP User deprecated'],
        );
    }

    public function testUnknownPhpError()
    {
        $record = array(
            'channel'    => 'psx',
            'level_name' => 'INFO',
            'message'    => 'foo',
            'context'    => array(
                'severity' => 'foo',
            ),
        );

        $this->assertEquals('psx.INFO: foo', $this->formatter->format($record));
    }
}
