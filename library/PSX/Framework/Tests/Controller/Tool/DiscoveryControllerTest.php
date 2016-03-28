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

namespace PSX\Framework\Tests\Controller\Tool;

use PSX\Json\Parser;
use PSX\Framework\Test\ControllerTestCase;

/**
 * DiscoveryControllerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DiscoveryControllerTest extends ControllerTestCase
{
    public function testIndex()
    {
        $response = $this->sendRequest('http://127.0.0.1/discovery', 'GET');
        $json     = (string) $response->getBody();

        $expect = <<<'JSON'
{
    "links": [
        {
            "rel": "api",
            "href": "http:\/\/127.0.0.1\/"
        },
        {
            "rel": "routing",
            "href": "http:\/\/127.0.0.1\/routing"
        },
        {
            "rel": "documentation",
            "href": "http:\/\/127.0.0.1\/doc"
        },
        {
            "rel": "raml",
            "href": "http:\/\/127.0.0.1\/raml"
        },
        {
            "rel": "wsdl",
            "href": "http:\/\/127.0.0.1\/wsdl"
        },
        {
            "rel": "swagger",
            "href": "http:\/\/127.0.0.1\/swagger"
        }
    ]
}
JSON;

        $this->assertEquals(null, $response->getStatusCode(), $json);
        $this->assertJsonStringEqualsJsonString($expect, $json, $json);
    }

    protected function getPaths()
    {
        return array(
            [['GET'], '/discovery', 'PSX\Framework\Controller\Tool\DiscoveryController'],
            [['GET'], '/routing', 'PSX\Framework\Controller\Tool\RoutingController'],
            [['GET'], '/doc', 'PSX\Framework\Controller\Tool\DocumentationController::doIndex'],
            [['GET'], '/doc/:version/*path', 'PSX\Framework\Controller\Tool\DocumentationController::doDetail'],
            [['GET'], '/raml', 'PSX\Framework\Controller\Generator\RamlController'],
            [['GET'], '/wsdl', 'PSX\Framework\Controller\Generator\WsdlController'],
            [['GET'], '/swagger', 'PSX\Framework\Controller\Generator\SwaggerController::doDetail'],
        );
    }
}
