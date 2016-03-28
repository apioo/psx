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

namespace PSX\Framework\Tests\Loader\CallbackResolver;

use PSX\Framework\Loader\CallbackResolver\DependencyInjector;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Framework\Loader\Context;
use PSX\Framework\Test\Environment;
use PSX\Uri\Url;

/**
 * DependencyInjectorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DependencyInjectorTest extends \PHPUnit_Framework_TestCase
{
    public function testResolve()
    {
        $context  = new Context();
        $context->set(Context::KEY_SOURCE, 'PSX\Framework\Tests\Loader\CallbackResolver\TestController::doIndex');

        $request  = new Request(new Url('http://127.0.0.1'), 'GET');
        $response = new Response();

        $simple     = new DependencyInjector(Environment::getService('controller_factory'));
        $controller = $simple->resolve($request, $response, $context);

        $this->assertInstanceOf('PSX\Framework\Tests\Loader\CallbackResolver\TestController', $controller);
        $this->assertEquals('PSX\Framework\Tests\Loader\CallbackResolver\TestController', $context->get(Context::KEY_CLASS));
        $this->assertEquals('doIndex', $context->get(Context::KEY_METHOD));
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testInvalidSource()
    {
        $context  = new Context();
        $context->set(Context::KEY_SOURCE, 'foobar');

        $request  = new Request(new Url('http://127.0.0.1'), 'GET');
        $response = new Response();

        $simple = new DependencyInjector(Environment::getService('controller_factory'));
        $simple->resolve($request, $response, $context);
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testClassNotExist()
    {
        $context  = new Context();
        $context->set(Context::KEY_SOURCE, 'Foo::bar');

        $request  = new Request(new Url('http://127.0.0.1'), 'GET');
        $response = new Response();

        $simple = new DependencyInjector(Environment::getService('controller_factory'));
        $simple->resolve($request, $response, $context);
    }
}
