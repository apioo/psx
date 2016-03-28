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

namespace PSX\Framework\Tests\Dependency;

use PSX\Framework\Dependency\Container;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Scope;

/**
 * Most tests are taken from the symfony di container test
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testSet()
    {
        $sc = new Container();
        $sc->set('foo', $foo = new \stdClass());
        $this->assertEquals($foo, $sc->get('foo'), '->set() sets a service');
    }

    public function testSetWithNullResetTheService()
    {
        $sc = new Container();
        $sc->set('foo', null);
        $this->assertFalse($sc->has('foo'));
    }

    public function testGet()
    {
        $sc = new ProjectServiceContainer();
        $sc->set('foo', $foo = new \stdClass());
        $this->assertEquals($foo, $sc->get('foo'), '->get() returns the service for the given id');
        $this->assertEquals($sc->__bar, $sc->get('bar'), '->get() returns the service for the given id');
        $this->assertEquals($sc->__foo_bar, $sc->get('fooBar'), '->get() returns the service if a get*Method() is defined');
        $this->assertEquals($sc->__foo_bar, $sc->get('foo_bar'), '->get() returns the service if a get*Method() is defined');

        $sc->set('bar', $bar = new \stdClass());
        $this->assertEquals($bar, $sc->get('bar'), '->get() prefers to return a service defined with set() than one defined with a getXXXMethod()');
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function testGetThrowServiceNotFoundException()
    {
        $sc = new Container();
        $sc->get('foo');
    }

    public function testGetSetParameter()
    {
        $sc = new Container();
        $sc->setParameter('bar', 'foo');
        $this->assertEquals('foo', $sc->getParameter('bar'), '->setParameter() sets the value of a new parameter');

        $sc->setParameter('foo', 'baz');
        $this->assertEquals('baz', $sc->getParameter('foo'), '->setParameter() overrides previously set parameter');

        $sc->setParameter('Foo', 'baz1');
        $this->assertEquals('baz1', $sc->getParameter('foo'), '->setParameter() converts the key to lowercase');
        $this->assertEquals('baz1', $sc->getParameter('FOO'), '->getParameter() converts the key to lowercase');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidGetParameter()
    {
        $sc = new Container();
        $sc->getParameter('foobar');
    }

    public function testHas()
    {
        $sc = new ProjectServiceContainer();
        $sc->set('foo', new \stdClass());
        $this->assertFalse($sc->has('foo1'), '->has() returns false if the service does not exist');
        $this->assertTrue($sc->has('foo'), '->has() returns true if the service exists');
        $this->assertTrue($sc->has('bar'), '->has() returns true if a get*Method() is defined');
        $this->assertTrue($sc->has('fooBar'), '->has() returns true if a get*Method() is defined');
        $this->assertTrue($sc->has('foo_bar'), '->has() returns true if a get*Method() is defined');
    }

    public function testEnterLeaveCurrentScope()
    {
        $container = new ProjectServiceContainer();
        $container->addScope(new Scope('foo'));
        $container->set('scoped', new \stdClass(), 'foo');
        $container->set('scoped_foo', new \stdClass(), 'foo');

        $this->assertEquals($container->__bar, $container->get('bar'));

        try {
            $this->assertInstanceOf('stdClass', $container->get('scoped'));
            $this->fail('Scoped service should not be accessible from default scope');
        } catch (ServiceNotFoundException $e) {
        }

        $container->enterScope('foo');

        $this->assertInstanceOf('stdClass', $container->get('scoped'));
        $this->assertInstanceOf('stdClass', $container->get('scoped_foo'));

        $container->leaveScope('foo');

        $this->assertEquals($container->__bar, $container->get('bar'));

        try {
            $this->assertInstanceOf('stdClass', $container->get('scoped'));
            $this->fail('Scoped service should not be accessible after leaving a scope');
        } catch (ServiceNotFoundException $e) {
        }
    }

    public function testAddScope()
    {
        $sc = new Container();
        $sc->addScope(new Scope('foo'));
        $sc->addScope(new Scope('bar'));

        $this->assertTrue($sc->hasScope('foo'));
        $this->assertTrue($sc->hasScope('bar'));
    }

    public function testHasScope()
    {
        $sc = new Container();

        $this->assertFalse($sc->hasScope('foo'));
        $sc->addScope(new Scope('foo'));
        $this->assertTrue($sc->hasScope('foo'));
    }

    public function testIsScopeActive()
    {
        $sc = new Container();

        $this->assertFalse($sc->isScopeActive('foo'));
        $sc->addScope(new Scope('foo'));

        $this->assertFalse($sc->isScopeActive('foo'));
        $sc->enterScope('foo');

        $this->assertTrue($sc->isScopeActive('foo'));
        $sc->leaveScope('foo');

        $this->assertFalse($sc->isScopeActive('foo'));
    }

    public function testGetServiceIds()
    {
        $sc = new ProjectServiceContainer();

        $services = $sc->getServiceIds();

        $this->assertEquals(array('bar', 'foo_bar', 'scalar'), $services);
    }

    public function testGetReturnType()
    {
        $sc = new ProjectServiceContainer();

        $this->assertEquals('stdClass', $sc->getReturnType('bar'));
        $this->assertEquals('\stdClass', $sc->getReturnType('foo_bar'));
        $this->assertEquals('array', $sc->getReturnType('scalar'));
    }
}

class ProjectServiceContainer extends Container
{
    public $__bar, $__foo_bar;

    public function __construct()
    {
        parent::__construct();

        $this->__bar = new \stdClass();
        $this->__foo_bar = new \stdClass();
    }

    protected function getBar()
    {
        return $this->__bar;
    }

    /**
     * @return \stdClass
     */
    protected function getFooBar()
    {
        return $this->__foo_bar;
    }

    protected function getScalar()
    {
        return array('foo', 'bar');
    }
}
