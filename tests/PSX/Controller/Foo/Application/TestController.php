<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Controller\Foo\Application;

use DOMDocument;
use PSX\ControllerAbstract;
use PSX\Data\ReaderInterface;
use PSX\Data\Record;
use PSX\Filter;
use PSX\Http\Stream\FileStream;
use PSX\Loader\Location;
use PSX\Url;
use PSX\Validate;
use SimpleXMLElement;

/**
 * TestController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TestController extends ControllerAbstract
{
	/**
	 * @Inject
	 * @var PHPUnit_Framework_TestCase
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
		$data = array(
			'foo' => 'bar',
			'bar' => array('foo' => 'nested'),
			'entries' => array(array('title' => 'bar'), array('title' => 'foo')),
		);

		$this->testCase->assertEquals($data, $this->getBody());
		$this->testCase->assertEquals($data, $this->getBody(ReaderInterface::JSON));

		// accessor
		$this->testCase->assertEquals('bar', $this->getAccessor()->get('foo'));
		$this->testCase->assertEquals('nested', $this->getAccessor()->get('bar.foo'));
		$this->testCase->assertEquals('bar', $this->getAccessor()->get('entries.0.title'));
		$this->testCase->assertEquals('foo', $this->getAccessor()->get('entries.1.title'));

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
		$this->testCase->assertInstanceOf('PSX\Loader\Location', $this->location);
		$this->testCase->assertEquals('PSX\Controller\Foo\Application\TestController::doInspect', $this->location->getParameter(Location::KEY_SOURCE));
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
		$this->setBody(new \stdClass());
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

	public function getPreFilter()
	{
		return array(function($request, $response, $stack){

			$this->testCase->assertInstanceOf('PSX\Http\Request', $request);
			$this->testCase->assertInstanceOf('PSX\Http\Response', $response);

			$stack->handle($request, $response);

		});
	}

	public function getPostFilter()
	{
		return array(function($request, $response, $stack){

			$this->testCase->assertInstanceOf('PSX\Http\Request', $request);
			$this->testCase->assertInstanceOf('PSX\Http\Response', $response);

			$stack->handle($request, $response);

		});
	}
}
