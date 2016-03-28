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

namespace PSX\Framework\Test;

use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Uri\Url;

/**
 * ControllerTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class ControllerTestCase extends \PHPUnit_Framework_TestCase
{
    use ContainerTestCaseTrait;

    /**
     * Loads a specific controller
     *
     * @param \PSX\Http\Request $request
     * @param \PSX\Http\Response $response
     * @return \PSX\Framework\Controller\ControllerInterface
     */
    protected function loadController(Request $request, Response $response)
    {
        return Environment::getService('dispatch')->route($request, $response);
    }

    /**
     * Sends a request to the system and returns the http response
     *
     * @param string $url
     * @param string $method
     * @param array $headers
     * @param string $body
     * @return \PSX\Http\ResponseInterface
     */
    protected function sendRequest($url, $method, $headers = array(), $body = null)
    {
        $request  = new Request(is_string($url) ? new Url($url) : $url, $method, $headers, $body);
        $response = new Response();
        $response->setBody(new TempStream(fopen('php://memory', 'r+')));

        $this->loadController($request, $response);

        return $response;
    }

    /**
     * Removes any system specific parts of an exception response
     *
     * @param string $body
     * @return string
     */
    public static function normalizeExceptionResponse($body)
    {
        $body = preg_replace('/ in (.*) on line (\d+)/', '', $body);
        $body = preg_replace('/"trace": "(.*)"/', '"trace": ""', $body);
        $body = preg_replace('/"context": "(.*)"/', '"context": ""', $body);

        return $body;
    }
}
