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

namespace PSX\Api\Resource\Generator\Wsdl;

use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Test\ControllerDbTestCase;
use PSX\Test\Environment;
use PSX\Url;
use SoapClient;
use stdClass;

/**
 * This test checks the SOAP/WSDL implementation of PSX. It uses the generated
 * WSDL from the WsdlGeneratorController to create an SoapClient. The SoapClient
 * requests then an API endpoint
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SoapEndpointTest extends ControllerDbTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/../../../../Sql/table_fixture.xml');
    }

    public function testGetCollection()
    {
        $collection = $this->getSoapClient()->getCollection();

        $this->assertEquals($this->getExpectedCollection(), $collection);
    }

    public function testPostItem()
    {
        $entry = new stdClass();
        $entry->userId = 3;
        $entry->title = 'test';
        $entry->date = '2013-04-29T16:56:32Z';

        $message = $this->getSoapClient()->postItem($entry);

        $this->assertTrue($message->success);
        $this->assertEquals('You have successful post a record', $message->message);
    }

    /**
     * @expectedException \SoapFault
     */
    public function testPostItemInvalid()
    {
        $entry = new stdClass();
        $entry->userId = 3;
        $entry->title = 'fo';
        $entry->date = '2013-04-29T16:56:32Z';

        $this->getSoapClient()->postItem($entry);
    }

    public function testPutItem()
    {
        $entry = new stdClass();
        $entry->id = 1;
        $entry->userId = 3;
        $entry->title = 'foobar';
        $entry->date = '2013-04-29T16:56:32Z';

        $message = $this->getSoapClient()->putItem($entry);

        $this->assertTrue($message->success);
        $this->assertEquals('You have successful put a record', $message->message);
    }

    public function testDeleteItem()
    {
        $entry = new stdClass();
        $entry->id = 1;

        $message = $this->getSoapClient()->deleteItem($entry);

        $this->assertTrue($message->success);
        $this->assertEquals('You have successful delete a record', $message->message);
    }

    public function doLoadController($request, $response)
    {
        return $this->loadController($request, $response);
    }

    protected function getSoapClient()
    {
        $wsdl    = 'data://text/plain;base64,' . base64_encode($this->getWsdl());
        $options = array(
            'location' => 'http://127.0.0.1/api',
            'uri'      => Environment::getService('config')->get('psx_url'),
        );

        return new TestSoapClient($wsdl, $options, $this);
    }

    protected function getWsdl()
    {
        return (string) $this->sendRequest('http://127.0.0.1/wsdl/1/api', 'GET')->getBody();
    }

    protected function getPaths()
    {
        return array(
            [['GET', 'POST', 'PUT', 'DELETE'], '/api', 'PSX\Controller\Foo\Application\TestSchemaApiController'],
            [['GET'], '/wsdl/:version/*path', 'PSX\Controller\Tool\WsdlGeneratorController'],
        );
    }

    protected function getExpectedCollection()
    {
        $collection = new stdClass();
        $collection->entry = array();

        $entry = new stdClass();
        $entry->id = 4;
        $entry->userId = 3;
        $entry->title = 'blub';
        $entry->date = '2013-04-29T16:56:32Z';

        $collection->entry[] = $entry;

        $entry = new stdClass();
        $entry->id = 3;
        $entry->userId = 2;
        $entry->title = 'test';
        $entry->date = '2013-04-29T16:56:32Z';

        $collection->entry[] = $entry;

        $entry = new stdClass();
        $entry->id = 2;
        $entry->userId = 1;
        $entry->title = 'bar';
        $entry->date = '2013-04-29T16:56:32Z';

        $collection->entry[] = $entry;

        $entry = new stdClass();
        $entry->id = 1;
        $entry->userId = 1;
        $entry->title = 'foo';
        $entry->date = '2013-04-29T16:56:32Z';

        $collection->entry[] = $entry;

        return $collection;
    }
}

class TestSoapClient extends SoapClient
{
    protected $testCase;

    public function __construct($wsdl, array $options, SoapEndpointTest $testCase)
    {
        parent::__construct($wsdl, $options);

        $this->testCase = $testCase;
    }

    public function __doRequest($body, $location, $action, $version, $oneWay = null)
    {
        $method   = parse_url($action, PHP_URL_FRAGMENT);
        $header   = array(
            'Content-Type' => 'application/soap+xml',
            'Accept'       => 'application/soap+xml',
        );
        $request  = new Request(new Url($location), $method, $header, $body);

        $body     = new TempStream(fopen('php://memory', 'r+'));
        $response = new Response();
        $response->setBody($body);

        $controller = $this->testCase->doLoadController($request, $response);
        $body       = (string) $response->getBody();

        return $body;
    }
}
