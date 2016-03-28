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
 * ControllerDbTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class ControllerDbTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    use ContainerTestCaseTrait;

    protected static $con;

    protected $connection;

    public function getConnection()
    {
        if (!Environment::hasConnection()) {
            $this->markTestSkipped('No database connection available');
        }

        if (self::$con === null) {
            self::$con = Environment::getService('connection');
        }

        if ($this->connection === null) {
            $this->connection = self::$con;
        }

        return $this->createDefaultDBConnection($this->connection->getWrappedConnection(), Environment::getService('config')->get('psx_sql_db'));
    }

    /**
     * Loads an specific controller
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
     * Sends an request to the system and returns the http response
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
}
