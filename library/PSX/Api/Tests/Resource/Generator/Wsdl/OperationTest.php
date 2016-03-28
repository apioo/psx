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

namespace PSX\Api\Tests\Resource\Generator\Wsdl;

use PSX\Api\Resource\Generator\Wsdl\Operation;

/**
 * OperationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class OperationTest extends \PHPUnit_Framework_TestCase
{
    public function testIn()
    {
        $operation = new Operation('getEntry');
        $operation->setMethod('GET');
        $operation->setIn('void');
        $operation->setOut('collection');

        $this->assertEquals('getEntry', $operation->getName());
        $this->assertEquals('GET', $operation->getMethod());
        $this->assertEquals('void', $operation->getIn());
        $this->assertTrue($operation->hasIn());
        $this->assertEquals('collection', $operation->getOut());
        $this->assertTrue($operation->hasOut());

        $this->assertTrue($operation->hasOperation());
        $this->assertFalse($operation->isInOnly());
        $this->assertFalse($operation->isOutOnly());
        $this->assertTrue($operation->isInOut());
    }
}
