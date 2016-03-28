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

namespace PSX\Framework\Tests\Loader;

use PSX\Framework\Loader\ReverseRouter;
use PSX\Framework\Loader\RoutingParser\RoutingFile;

/**
 * ReverseRouterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ReverseRouterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPathRoutes()
    {
        $routingFile = new RoutingFile(__DIR__ . '/routes');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertEquals('/', $router->getPath('PSX\Framework\Loader\Foo1Controller'));
        $this->assertEquals('/foo/bar', $router->getPath('PSX\Framework\Loader\Foo2Controller'));
        $this->assertEquals('/foo/test', $router->getPath('PSX\Framework\Loader\Foo3Controller', ['test']));
        $this->assertEquals('/foo/bla/blub', $router->getPath('PSX\Framework\Loader\Foo4Controller', ['bla', 'blub']));
        $this->assertEquals('/bar', $router->getPath('PSX\Framework\Loader\Foo5Controller'));
        $this->assertEquals('/bar/foo', $router->getPath('PSX\Framework\Loader\Foo6Controller'));
        $this->assertEquals('/bar/12', $router->getPath('PSX\Framework\Loader\Foo7Controller', [12]));
        $this->assertEquals('/bar/37/13', $router->getPath('PSX\Framework\Loader\Foo8Controller', ['bar' => 13, 'foo' => 37]));
        $this->assertEquals('/bar', $router->getPath('PSX\Framework\Loader\Foo9Controller'));
        $this->assertEquals('/whitespace', $router->getPath('PSX\Framework\Loader\Foo10Controller'));
        $this->assertEquals('/test', $router->getPath('PSX\Framework\Loader\Foo11Controller'));
        $this->assertEquals('/files/foo/bar', $router->getPath('PSX\Framework\Loader\Foo12Controller', ['path' => 'foo/bar']));
        $this->assertEquals('http://cdn.foo.com/serve/foo/common.js', $router->getPath('PSX\Framework\Loader\Foo13Controller', ['path' => 'foo/common.js']));
        $this->assertEquals('/baz', $router->getPath('PSX\Framework\Loader\Foo14Controller', []));
    }

    public function testGetPathNamedParameter()
    {
        $routingFile = new RoutingFile(__DIR__ . '/routes');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertEquals('/foo/bla/blub', $router->getPath('PSX\Framework\Loader\Foo4Controller', ['foo' => 'blub', 'bar' => 'bla']));
    }

    public function testGetPathIndexedParameter()
    {
        $routingFile = new RoutingFile(__DIR__ . '/routes');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertEquals('/foo/bla/blub', $router->getPath('PSX\Framework\Loader\Foo4Controller', ['bla', 'blub']));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetPathMissingParameter()
    {
        $routingFile = new RoutingFile(__DIR__ . '/routes');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $router->getPath('PSX\Framework\Loader\Foo4Controller', ['bla']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetPathRegExpMissingParameter()
    {
        $routingFile = new RoutingFile(__DIR__ . '/routes');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $router->getPath('PSX\Framework\Loader\Foo8Controller', ['bla']);
    }

    public function testGetNotExisting()
    {
        $routingFile = new RoutingFile(__DIR__ . '/routes');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertNull($router->getPath('Foo\Bar'));
        $this->assertNull($router->getAbsolutePath('Foo\Bar'));
        $this->assertNull($router->getUrl('Foo\Bar'));
    }

    public function testGetPath()
    {
        $routingFile = new RoutingFile(__DIR__ . '/routes');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertEquals('/foo/bar', $router->getPath('PSX\Framework\Loader\Foo2Controller'));

        $routingFile = new RoutingFile(__DIR__ . '/routes');
        $router      = new ReverseRouter($routingFile, 'http://foo.com/foo/bar', '');

        $this->assertEquals('/foo/bar', $router->getPath('PSX\Framework\Loader\Foo2Controller'));

        $routingFile = new RoutingFile(__DIR__ . '/routes');
        $router      = new ReverseRouter($routingFile, 'http://foo.com/foo/bar', 'index.php/');

        $this->assertEquals('/foo/bar', $router->getPath('PSX\Framework\Loader\Foo2Controller'));

        $routingFile = new RoutingFile(__DIR__ . '/routes');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertEquals('http://cdn.foo.com/serve/foo/common.js', $router->getPath('PSX\Framework\Loader\Foo13Controller', ['path' => 'foo/common.js']));
    }

    public function testGetAbsolutePath()
    {
        $routingFile = new RoutingFile(__DIR__ . '/routes');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertEquals('/foo/bar', $router->getAbsolutePath('PSX\Framework\Loader\Foo2Controller'));

        $routingFile = new RoutingFile(__DIR__ . '/routes');
        $router      = new ReverseRouter($routingFile, 'http://foo.com/foo/bar', '');

        $this->assertEquals('/foo/bar/foo/bar', $router->getAbsolutePath('PSX\Framework\Loader\Foo2Controller'));

        $routingFile = new RoutingFile(__DIR__ . '/routes');
        $router      = new ReverseRouter($routingFile, 'http://foo.com/foo/bar', 'index.php/');

        $this->assertEquals('/foo/bar/index.php/foo/bar', $router->getAbsolutePath('PSX\Framework\Loader\Foo2Controller'));

        $routingFile = new RoutingFile(__DIR__ . '/routes');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertEquals('http://cdn.foo.com/serve/foo/common.js', $router->getAbsolutePath('PSX\Framework\Loader\Foo13Controller', ['path' => 'foo/common.js']));
    }

    public function testGetUrl()
    {
        $routingFile = new RoutingFile(__DIR__ . '/routes');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertEquals('http://foo.com/foo/bar', $router->getUrl('PSX\Framework\Loader\Foo2Controller'));

        $routingFile = new RoutingFile(__DIR__ . '/routes');
        $router      = new ReverseRouter($routingFile, 'http://foo.com/foo/bar', '');

        $this->assertEquals('http://foo.com/foo/bar/foo/bar', $router->getUrl('PSX\Framework\Loader\Foo2Controller'));

        $routingFile = new RoutingFile(__DIR__ . '/routes');
        $router      = new ReverseRouter($routingFile, 'http://foo.com/foo/bar', 'index.php/');

        $this->assertEquals('http://foo.com/foo/bar/index.php/foo/bar', $router->getUrl('PSX\Framework\Loader\Foo2Controller'));

        $routingFile = new RoutingFile(__DIR__ . '/routes');
        $router      = new ReverseRouter($routingFile, 'http://foo.com', '');

        $this->assertEquals('http://cdn.foo.com/serve/foo/common.js', $router->getUrl('PSX\Framework\Loader\Foo13Controller', ['path' => 'foo/common.js']));
    }
}
