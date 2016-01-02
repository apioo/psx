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

namespace PSX\Controller\Foo\Application;

use DOMDocument;
use PSX\ControllerAbstract;
use PSX\Data\ReaderInterface;
use PSX\Data\Record;
use PSX\Data\WriterInterface;
use PSX\Filter;
use PSX\Http\Stream\FileStream;
use PSX\Loader\Context;
use PSX\Url;
use PSX\Validate;
use SimpleXMLElement;

/**
 * TestController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TestController extends ControllerAbstract
{
    /**
     * @Inject
     * @var \PHPUnit_Framework_TestCase
     */
    protected $testCase;

    public function doIndex()
    {
        $this->response->getBody()->write('foobar');
    }

    public function doInspect()
    {
        // get uri fragments
        $this->testCase->assertEquals(null, $this->getUriFragment('foo'));

        // set response code
        $this->setResponseCode(200);

        $this->testCase->assertEquals(200, $this->response->getStatusCode());

        // set header
        $this->setHeader('Content-Type', 'application/xml');

        $this->testCase->assertEquals('application/xml', $this->response->getHeader('Content-Type'));

        // get method
        $this->testCase->assertEquals('POST', $this->getMethod());

        // get uri
        $this->testCase->assertInstanceOf('PSX\Uri', $this->getUri());

        // get header
        $this->testCase->assertEquals(null, $this->getHeader('foo'));

        // has header
        $this->testCase->assertEquals(false, $this->hasHeader('foo'));

        // get parameter
        $this->testCase->assertEquals('bar', $this->getParameter('foo'));
        $this->testCase->assertEquals('bar', $this->getParameter('foo', Validate::TYPE_STRING));
        $this->testCase->assertEquals('bar', $this->getParameter('foo', Validate::TYPE_STRING, array(new Filter\Alnum())));
        $this->testCase->assertEquals('bar', $this->getParameter('foo', Validate::TYPE_STRING, array(), 'Foo'));
        $this->testCase->assertEquals('bar', $this->getParameter('foo', Validate::TYPE_STRING, array(), 'Foo', true));

        // get body
        $data = new \stdClass();
        $data->foo = 'bar';
        $data->bar = new \stdClass();
        $data->bar->foo = 'nested';
        $data->entries = [];
        $data->entries[0] = new \stdClass();
        $data->entries[0]->title = 'bar';
        $data->entries[1] = new \stdClass();
        $data->entries[1]->title = 'foo';

        $this->testCase->assertEquals($data, $this->getBody());
        $this->testCase->assertEquals($data, $this->getBody(ReaderInterface::JSON));

        // accessor
        $this->testCase->assertEquals('bar', $this->getAccessor()->get('/foo'));
        $this->testCase->assertEquals('nested', $this->getAccessor()->get('/bar/foo'));
        $this->testCase->assertEquals('bar', $this->getAccessor()->get('/entries/0/title'));
        $this->testCase->assertEquals('foo', $this->getAccessor()->get('/entries/1/title'));

        // import
        $record = new Record('foo', array('foo' => null));

        $this->testCase->assertInstanceOf('PSX\Data\Record', $this->import($record));
        $this->testCase->assertEquals('bar', $record->getFoo());

        // set response
        $record = new Record('foo', array('bar' => 'foo'));

        $this->setBody($record);

        // is writer
        $this->testCase->assertTrue($this->isWriter('PSX\Data\Writer\Json'));

        // is reader
        $this->testCase->assertTrue($this->isReader('PSX\Data\Reader\Json'));

        // get supported writer
        $this->testCase->assertEquals(null, $this->getSupportedWriter());

        // test properties
        $this->testCase->assertInstanceOf('PSX\Loader\Context', $this->context);
        $this->testCase->assertEquals('PSX\Controller\Foo\Application\TestController::doInspect', $this->context->get(Context::KEY_SOURCE));
        $this->testCase->assertInstanceOf('PSX\Http\Request', $this->request);
        $this->testCase->assertInstanceOf('PSX\Http\Response', $this->response);
        $this->testCase->assertTrue(is_array($this->uriFragments));
        $this->testCase->assertInstanceOf('PSX\Config', $this->config);
        $this->testCase->assertInstanceOf('PSX\Validate', $this->validate);
        $this->testCase->assertInstanceOf('PSX\Loader', $this->loader);
        $this->testCase->assertInstanceOf('PSX\Loader\ReverseRouter', $this->reverseRouter);
        $this->testCase->assertInstanceOf('PSX\Data\ReaderFactory', $this->readerFactory);
        $this->testCase->assertInstanceOf('PSX\Data\WriterFactory', $this->writerFactory);
        $this->testCase->assertInstanceOf('PSX\Data\Importer', $this->importer);
        $this->testCase->assertInstanceOf('PSX\Data\Extractor', $this->extractor);
    }

    public function doForward()
    {
        $this->forward('PSX\Controller\Foo\Application\TestController::doRedirectDestiniation', array('foo' => 'bar'));
    }

    /**
     * Should throw an exception
     */
    public function doForwardInvalidRoute()
    {
        $this->forward('Foo\Bar');
    }

    public function doRedirect()
    {
        $this->redirect('PSX\Controller\Foo\Application\TestController::doRedirectDestiniation', array('foo' => 'bar'));
    }

    public function doRedirectAbsoluteString()
    {
        $this->redirect('http://localhost.com/foobar');
    }

    public function doRedirectAbsoluteObject()
    {
        $this->redirect(new Url('http://localhost.com/foobar'));
    }

    public function doSetArrayBody()
    {
        $this->setBody(array('foo' => array('bar')));
    }

    public function doSetStdClassBody()
    {
        $body = new \stdClass();
        $body->foo = array('bar');

        $this->setBody($body);
    }

    public function doSetRecordBody()
    {
        $this->setBody(new Record('record', array('foo' => array('bar'))));
    }

    public function doSetDomDocumentBody()
    {
        $dom = new DOMDocument();
        $dom->appendChild($dom->createElement('foo', 'bar'));

        $this->setBody($dom);
    }

    public function doSetSimpleXmlBody()
    {
        $simpleXml = new SimpleXMLElement('<foo>bar</foo>');

        $this->setBody($simpleXml);
    }

    public function doSetStringBody()
    {
        $this->setBody('foobar');
    }

    public function doSetStreamBody()
    {
        $this->setBody(new FileStream(fopen(__DIR__ . '/../Resource/test_file', 'r'), 'foo.txt', 'application/octet-stream'));
    }

    /**
     * Should throw an exception
     */
    public function doSetInvalidBody()
    {
        $this->setBody(new \DateTime());
    }

    /**
     * Should only write foo once
     */
    public function doSetDoubleBody()
    {
        $this->setBody('foo');
        $this->setBody('foo');
    }

    public function doRedirectDestiniation()
    {
        $this->setBody($this->uriFragments);
    }

    /**
     * Tests whether the supported writer value was set by an origin controller
     */
    public function doInheritSupportedWriter()
    {
        $this->testCase->assertEquals([WriterInterface::XML], $this->getSupportedWriter());

        $this->setBody([
            'bar' => 'foo'
        ]);
    }

    public function getPreFilter()
    {
        return array(function ($request, $response, $stack) {

            $this->testCase->assertInstanceOf('PSX\Http\Request', $request);
            $this->testCase->assertInstanceOf('PSX\Http\Response', $response);

            $stack->handle($request, $response);

        });
    }

    public function getPostFilter()
    {
        return array(function ($request, $response, $stack) {

            $this->testCase->assertInstanceOf('PSX\Http\Request', $request);
            $this->testCase->assertInstanceOf('PSX\Http\Response', $response);

            $stack->handle($request, $response);

        });
    }
}
