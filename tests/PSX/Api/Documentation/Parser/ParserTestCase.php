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

namespace PSX\Api\Documentation\Parser;

use PSX\Data\SchemaInterface;
use PSX\Test\Environment;

/**
 * ParserTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class ParserTestCase extends \PHPUnit_Framework_TestCase
{
    public function testParseSimple()
    {
        $documentation = $this->getSimpleDocumentation();
        $resource      = $documentation->getResource(1);

        $this->assertEquals('/foo', $resource->getPath());
        $this->assertEquals('Test', $resource->getTitle());
        $this->assertEquals('Test description', $resource->getDescription());

        $path = $resource->getPathParameters();

        $this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $path->getDefinition());
        $this->assertInstanceOf('PSX\Data\Schema\Property\StringType', $path->getDefinition()->get('fooId'));

        $methods = $resource->getMethods();

        $this->assertEquals(['GET'], array_keys($methods));

        $this->assertEquals('Test description', $methods['GET']->getDescription());

        $query = $methods['GET']->getQueryParameters();

        $this->assertEquals('foo', $query->getDefinition()->get('foo')->getName());
        $this->assertEquals('Test', $query->getDefinition()->get('foo')->getDescription());
        $this->assertEquals('bar', $query->getDefinition()->get('bar')->getName());
        $this->assertEquals(true, $query->getDefinition()->get('bar')->isRequired());
        $this->assertEquals('baz', $query->getDefinition()->get('baz')->getName());
        $this->assertEquals(['foo', 'bar'], $query->getDefinition()->get('baz')->getEnumeration());
        $this->assertEquals('boz', $query->getDefinition()->get('boz')->getName());
        $this->assertEquals('[A-z]+', $query->getDefinition()->get('boz')->getPattern());
        $this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $query->getDefinition());
        $this->assertInstanceOf('PSX\Data\Schema\Property\IntegerType', $query->getDefinition()->get('integer'));
        $this->assertInstanceOf('PSX\Data\Schema\Property\FloatType', $query->getDefinition()->get('number'));
        $this->assertInstanceOf('PSX\Data\Schema\Property\DateTimeType', $query->getDefinition()->get('date'));
        $this->assertInstanceOf('PSX\Data\Schema\Property\BooleanType', $query->getDefinition()->get('boolean'));
        $this->assertInstanceOf('PSX\Data\Schema\Property\StringType', $query->getDefinition()->get('string'));

        $request = $methods['GET']->getRequest();

        $this->assertInstanceOf('PSX\Data\SchemaInterface', $request);

        $response = $methods['GET']->getResponse(200);

        $this->assertInstanceOf('PSX\Data\SchemaInterface', $response);
    }

    public function testParseVersion()
    {
        $documentation = $this->getVersionDocumentation();

        $this->assertEquals(3, $documentation->getLatestVersion());
        $this->assertTrue($documentation->hasResource(1));
        $this->assertTrue($documentation->hasResource(2));
        $this->assertTrue($documentation->hasResource(3));
        $this->assertTrue($documentation->hasResource(4));
        $this->assertFalse($documentation->hasResource(5));

        for ($i = 1; $i <= 4; $i++) {
            $resource = $documentation->getResource($i);

            $this->assertEquals('/foo', $resource->getPath());
            $this->assertEquals('Test v' . $i, $resource->getTitle());
            $this->assertEquals('Test description v' . $i, $resource->getDescription());

            $path = $resource->getPathParameters();

            $this->assertInstanceOf('PSX\Data\Schema\Property\ComplexType', $path->getDefinition());
            $this->assertInstanceOf('PSX\Data\Schema\Property\StringType', $path->getDefinition()->get('fooId' . $i));

            $methods = $resource->getMethods();

            $this->assertEquals(['GET'], array_keys($methods));

            $this->assertEquals('Test description v' . $i, $methods['GET']->getDescription());

            $query = $methods['GET']->getQueryParameters();

            $this->assertInstanceOf('PSX\Data\Schema\Property\StringType', $query->getDefinition()->get('string' . $i));

            $request = $methods['GET']->getRequest();

            $this->assertInstanceOf('PSX\Data\SchemaInterface', $request);
            $this->assertInstanceOf('PSX\Data\Schema\Property\StringType', $request->getDefinition()->get('title' . $i));

            $response = $methods['GET']->getResponse(200);

            $this->assertInstanceOf('PSX\Data\SchemaInterface', $response);
            $this->assertInstanceOf('PSX\Data\Schema\Property\StringType', $request->getDefinition()->get('title' . $i));
        }
    }

    abstract protected function getSimpleDocumentation();

    abstract protected function getVersionDocumentation();
}
