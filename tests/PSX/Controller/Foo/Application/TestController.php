<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

		// get method
		$this->testCase->assertEquals('POST', $this->getMethod());

		// get url
		$this->testCase->assertInstanceOf('PSX\Url', $this->getUrl());

		// get header
		$this->testCase->assertEquals(null, $this->getHeader('foo'));

		// get parameter
		$this->testCase->assertEquals('bar', $this->getParameter('foo'));
		$this->testCase->assertEquals('bar', $this->getParameter('foo', Validate::TYPE_STRING));
		$this->testCase->assertEquals('bar', $this->getParameter('foo', Validate::TYPE_STRING, array(new Filter\Alnum())));
		$this->testCase->assertEquals('bar', $this->getParameter('foo', Validate::TYPE_STRING, array(), 'Foo'));
		$this->testCase->assertEquals('bar', $this->getParameter('foo', Validate::TYPE_STRING, array(), 'Foo', true));

		// get body
		$this->testCase->assertEquals(array('foo' => 'bar'), $this->getBody());
		$this->testCase->assertEquals(array('foo' => 'bar'), $this->getBody(ReaderInterface::JSON));

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
		$this->testCase->assertTrue($this->isReader('PSX\Data\Writer\Json'));

		// get preferred writer
		$this->testCase->assertInstanceOf('PSX\Data\Writer\Json', $this->getPreferredWriter());

		// get preferred writer
		$this->testCase->assertEquals(null, $this->getSupportedWriter());

		// test properties
		$this->testCase->assertInstanceOf('PSX\Loader\Location', $this->location);
		$this->testCase->assertEquals('PSX\Controller\Foo\Application\TestController::doInspect', $this->location->getParameter(Location::KEY_SOURCE));
		$this->testCase->assertInstanceOf('PSX\Http\Request', $this->request);
		$this->testCase->assertInstanceOf('PSX\Http\Response', $this->response);
		$this->testCase->assertTrue(is_array($this->uriFragments));
		$this->testCase->assertEquals(0x3F, $this->stage);
		$this->testCase->assertInstanceOf('PSX\Config', $this->config);
		$this->testCase->assertInstanceOf('PSX\Validate', $this->validate);
		$this->testCase->assertInstanceOf('PSX\Loader', $this->loader);
		$this->testCase->assertInstanceOf('PSX\Loader\ReverseRouter', $this->reverseRouter);
		$this->testCase->assertInstanceOf('PSX\Data\ReaderFactory', $this->readerFactory);
		$this->testCase->assertInstanceOf('PSX\Data\WriterFactory', $this->writerFactory);
	}

	public function doForward()
	{
		$this->forward('PSX\Controller\Foo\Application\TestController::doRedirectDestiniation', array('foo' => 'bar'));
	}

	public function doRedirect()
	{
		$this->redirect('PSX\Controller\Foo\Application\TestController::doRedirectDestiniation', array('foo' => 'bar'));
	}

	public function doRedirectAbsolute()
	{
		$this->redirect('http://localhost.com/foobar');
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

	public function doRedirectDestiniation()
	{
		$this->response->getBody()->write(json_encode($this->uriFragments));
	}

	public function getPreFilter()
	{
		$testCase = $this->testCase;

		return array(function($request, $response) use ($testCase){

			$testCase->assertInstanceOf('PSX\Http\Request', $request);
			$testCase->assertInstanceOf('PSX\Http\Response', $response);

		});
	}

	public function getPostFilter()
	{
		$testCase = $this->testCase;

		return array(function($request, $response) use ($testCase){

			$testCase->assertInstanceOf('PSX\Http\Request', $request);
			$testCase->assertInstanceOf('PSX\Http\Response', $response);

		});
	}
}
