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

use Monolog\Logger;

/**
 * LogCasterHandlerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class LogCasterHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $logCaster = $this->getMockBuilder('PSX\Framework\Log\LogCasterHandler')
            ->setMethods(array('connect', 'disconnect', 'writeLn', 'isAvailable'))
            ->getMock();

        $logCaster->expects($this->at(1))
            ->method('writeLn')
            ->with($this->equalTo('psx.INFO: foo in foo.php on line 12'));

        $logCaster->expects($this->at(2))
            ->method('writeLn')
            ->with($this->equalTo('trace'), $this->equalTo('#666'), $this->equalTo(false));

        $logCaster->expects($this->once())
            ->method('isAvailable')
            ->will($this->returnValue(true));

        $logger = new Logger('psx');
        $logger->pushHandler($logCaster);
        $logger->info('foo', array(
            'file'  => 'foo.php',
            'line'  => 12,
            'trace' => 'trace',
        ));
    }
}
