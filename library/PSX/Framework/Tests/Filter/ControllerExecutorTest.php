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

namespace PSX\Framework\Tests\Filter;

use PSX\Framework\Filter\ControllerExecutor;
use PSX\Framework\Filter\FilterChain;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Framework\Loader;
use PSX\Framework\Loader\Context;
use PSX\Uri\Url;

/**
 * ControllerExecutorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ControllerExecutorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider requestMethodProvider
     */
    public function testExecuteRequestMethod($method)
    {
        $request  = new Request(new Url('http://localhost'), $method);
        $response = new Response();

        $controller = $this->getMock('PSX\Framework\Controller\ControllerInterface', array(
            'onLoad',
            'onGet',
            'onHead',
            'onPost',
            'onPut',
            'onDelete',
            'onOptions',
            'onPatch',
            'processResponse',
        ));

        $controller->expects($this->once())
            ->method('onLoad');

        $controller->expects($this->once())
            ->method('on' . ucfirst(strtolower($method)));

        $controller->expects($this->once())
            ->method('processResponse');

        $filters = array();
        $filters[] = new ControllerExecutor($controller, new Context());

        $filterChain = new FilterChain($filters);
        $filterChain->handle($request, $response);
    }

    public function requestMethodProvider()
    {
        return array(
            ['GET'],
            ['HEAD'],
            ['POST'],
            ['PUT'],
            ['DELETE'],
            ['OPTIONS'],
            ['PATCH'],
        );
    }

    public function testExecuteControllerMethod()
    {
        $controller = $this->getMock('PSX\Framework\Controller\ControllerInterface', array(
            'onLoad',
            'onGet',
            'onHead',
            'onPost',
            'onPut',
            'onDelete',
            'onOptions',
            'onPatch',
            'processResponse',
            'doFoo',
        ));

        $controller->expects($this->once())
            ->method('onLoad');

        $controller->expects($this->once())
            ->method('onGet');

        $controller->expects($this->once())
            ->method('doFoo');

        $controller->expects($this->once())
            ->method('processResponse');

        $context = new Context();
        $context->set(Context::KEY_METHOD, 'doFoo');

        $request  = new Request(new Url('http://localhost'), 'GET');
        $response = new Response();

        $filters = array();
        $filters[] = new ControllerExecutor($controller, $context);

        $filterChain = new FilterChain($filters);
        $filterChain->handle($request, $response);
    }
}
