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

namespace PSX\Framework\Tests\Controller;

use PSX\Framework\Test\ControllerTestCase;
use PSX\Framework\Test\Environment;

/**
 * ViewAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ViewAbstractTest extends ControllerTestCase
{
    public function testAutomaticTemplateDetection()
    {
        $response = $this->sendRequest('http://127.0.0.1/view', 'GET', ['Accept' => 'text/html']);
        $body     = (string) $response->getBody();
        $data     = simplexml_load_string($body);

        $render = (float) $data->render;
        $config = Environment::getService('config');
        $base   = (string) parse_url($config['psx_url'], PHP_URL_PATH);

        $this->assertEquals('bar', $data->foo);
        $this->assertTrue(!empty($data->self));
        $this->assertEquals($config['psx_url'] . '/' . $config['psx_dispatch'], $data->url);
        $this->assertEquals($base, $data->base);
        $this->assertTrue($render > 0);
        $this->assertEquals('PSX/Framework/Tests/Controller/Foo/Resource', substr($data->location, -43));
    }

    public function testImplicitTemplate()
    {
        $response = $this->sendRequest('http://127.0.0.1/view/detail', 'GET', ['Accept' => 'text/html']);
        $data     = simplexml_load_string((string) $response->getBody());

        $render = (float) $data->render;
        $config = Environment::getService('config');
        $base   = (string) parse_url($config['psx_url'], PHP_URL_PATH);

        $this->assertEquals('bar', $data->foo);
        $this->assertTrue(!empty($data->self));
        $this->assertEquals($config['psx_url'] . '/' . $config['psx_dispatch'], $data->url);
        $this->assertEquals($base, $data->base);
        $this->assertTrue($render > 0);
        $this->assertEquals('PSX/Framework/Tests/Controller/Foo/Resource', substr($data->location, -43));
    }

    public function testExplicitTemplate()
    {
        $response = $this->sendRequest('http://127.0.0.1/view/explicit', 'GET', ['Accept' => 'text/html']);
        $data     = simplexml_load_string((string) $response->getBody());

        $render = (float) $data->render;
        $config = Environment::getService('config');
        $base   = (string) parse_url($config['psx_url'], PHP_URL_PATH);

        $this->assertEquals('bar', $data->foo);
        $this->assertTrue(!empty($data->self));
        $this->assertEquals($config['psx_url'] . '/' . $config['psx_dispatch'], $data->url);
        $this->assertEquals($base, $data->base);
        $this->assertTrue($render > 0);
        $this->assertEquals('PSX/Framework/Tests/Controller/Foo/Resource', substr($data->location, -43));
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/view', 'PSX\Framework\Tests\Controller\Foo\Application\TestViewController::doIndex'],
            [['GET'], '/view/detail', 'PSX\Framework\Tests\Controller\Foo\Application\TestViewController::doDetail'],
            [['GET'], '/view/explicit', 'PSX\Framework\Tests\Controller\Foo\Application\TestViewController::doExplicit'],
        );
    }
}
