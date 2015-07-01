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

namespace PSX\Command;

/**
 * ParameterBuilderTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ParameterBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testBuild()
    {
        $builder    = new ParameterBuilder();
        $parameters = $builder->setDescription('foobar')
            ->addOption('bar', Parameter::TYPE_REQUIRED, 'bar description')
            ->addOption('foo', Parameter::TYPE_OPTIONAL, 'foo description')
            ->addOption('v', Parameter::TYPE_FLAG, 'verbose')
            ->getParameters();

        $this->assertInstanceOf('PSX\Command\Parameters', $parameters);
        $this->assertEquals('foobar', $parameters->getDescription());
        $this->assertEquals(3, count($parameters));
        $this->assertEquals(null, $parameters->get('unknown'));

        $this->assertInstanceOf('PSX\Command\Parameter', $parameters[0]);
        $this->assertEquals('bar', $parameters[0]->getName());
        $this->assertEquals(Parameter::TYPE_REQUIRED, $parameters[0]->getType());
        $this->assertEquals('bar description', $parameters[0]->getDescription());
        $this->assertEquals(false, $parameters[0]->hasValue());
        $this->assertEquals(null, $parameters[0]->getValue());

        $this->assertInstanceOf('PSX\Command\Parameter', $parameters[1]);
        $this->assertEquals('foo', $parameters[1]->getName());
        $this->assertEquals(Parameter::TYPE_OPTIONAL, $parameters[1]->getType());
        $this->assertEquals('foo description', $parameters[1]->getDescription());
        $this->assertEquals(false, $parameters[1]->hasValue());
        $this->assertEquals(null, $parameters[1]->getValue());

        $this->assertInstanceOf('PSX\Command\Parameter', $parameters[2]);
        $this->assertEquals('v', $parameters[2]->getName());
        $this->assertEquals(Parameter::TYPE_FLAG, $parameters[2]->getType());
        $this->assertEquals('verbose', $parameters[2]->getDescription());
        $this->assertEquals(false, $parameters[2]->hasValue());
        $this->assertEquals(null, $parameters[2]->getValue());

        foreach ($parameters as $parameter) {
            $this->assertInstanceOf('PSX\Command\Parameter', $parameter);
        }
    }
}
