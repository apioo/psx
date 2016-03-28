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
use PSX\Framework\Log\LoggerFactory;

/**
 * LoggerFactoryTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class LoggerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider configProvider
     */
    public function testFactory($level, $handlerName, $uri)
    {
        $logger = LoggerFactory::factory($level, $handlerName, $uri);

        $this->assertInstanceOf('Monolog\Logger', $logger);

        foreach ($logger->getHandlers() as $handler) {
            $this->assertInstanceOf('Monolog\Handler\HandlerInterface', $handler);
        }
    }

    public function configProvider()
    {
        return [
            [Logger::ERROR, 'stream', 'foo.log'],
            [Logger::ERROR, 'void', null],
            [Logger::ERROR, 'system', null],
        ];
    }
}
