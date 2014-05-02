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
	public function doIndex()
	{
		$this->response->getBody()->write('foobar');
	}

	public function doInspect()
	{
		// inspect inner module API
		$testCase = $this->getTestCase();

		// get container
		$testCase->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerInterface', $this->getContainer());

		// get location
		$location = $this->getLocation();

		$testCase->assertInstanceOf('PSX\Loader\Location', $location);
		$testCase->assertEquals('PSX\Controller\Foo\Application\TestController::doInspect', $location->getSource());

		// get config
		$testCase->assertInstanceOf('PSX\Config', $this->getConfig());

		// get uri fragments
		$testCase->assertTrue(is_array($this->getUriFragments()));
		$testCase->assertEquals(null, $this->getUriFragments('foo'));

		// set response code
		$this->setResponseCode(200);

		// get method
		$testCase->assertEquals('POST', $this->getMethod());

		// get url
		$testCase->assertInstanceOf('PSX\Url', $this->getUrl());

		// get header
		$testCase->assertTrue(is_array($this->getHeader()));
		$testCase->assertEquals(null, $this->getHeader('foo'));

		// get parameter
		$testCase->assertEquals('bar', $this->getParameter('foo'));
		$testCase->assertEquals('bar', $this->getParameter('foo', Validate::TYPE_STRING));
		$testCase->assertEquals('bar', $this->getParameter('foo', Validate::TYPE_STRING, array(new Filter\Alnum())));
		$testCase->assertEquals('bar', $this->getParameter('foo', Validate::TYPE_STRING, array(), 'Foo'));
		$testCase->assertEquals('bar', $this->getParameter('foo', Validate::TYPE_STRING, array(), 'Foo', true));

		// get body
		$testCase->assertEquals(array('foo' => 'bar'), $this->getBody());
		$testCase->assertEquals(array('foo' => 'bar'), $this->getBody(ReaderInterface::JSON));

		// import
		$record = new Record('foo', array('foo' => null));

		$testCase->assertInstanceOf('PSX\Data\Record', $this->import($record));
		$testCase->assertEquals('bar', $record->getFoo());

		// get request reader
		$testCase->assertInstanceOf('PSX\Data\Reader\Json', $this->getRequestReader());
		$testCase->assertInstanceOf('PSX\Data\Reader\Json', $this->getRequestReader(ReaderInterface::JSON));

		// set response
		$record = new Record('foo', array('bar' => 'foo'));

		$this->setResponse($record);

		// get response writer
		$testCase->assertInstanceOf('PSX\Data\Writer\Json', $this->getResponseWriter());

		// is writer
		$testCase->assertTrue($this->isWriter('PSX\Data\Writer\Json'));

		// get preferred writer
		$testCase->assertInstanceOf('PSX\Data\Writer\Json', $this->getPreferredWriter());

		// get preferred writer
		$testCase->assertEquals(null, $this->getSupportedWriter());

		// test properties
		$testCase->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerInterface', $this->container);
		$testCase->assertInstanceOf('PSX\Loader\Location', $this->location);
		$testCase->assertInstanceOf('PSX\Http\Request', $this->request);
		$testCase->assertInstanceOf('PSX\Http\Response', $this->response);
		$testCase->assertTrue(is_array($this->uriFragments));
		$testCase->assertEquals(0x3F, $this->stage);
		$testCase->assertInstanceOf('PSX\Config', $this->config);
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

	public function doRedirectDestiniation()
	{
		$this->response->getBody()->write(json_encode($this->getUriFragments()));
	}

	public function getPreFilter()
	{
		$testCase = $this->getTestCase();

		return array(function($request, $response) use ($testCase){

			$testCase->assertInstanceOf('PSX\Http\Request', $request);
			$testCase->assertInstanceOf('PSX\Http\Response', $response);

		});
	}

	public function getPostFilter()
	{
		$testCase = $this->getTestCase();

		return array(function($request, $response) use ($testCase){

			$testCase->assertInstanceOf('PSX\Http\Request', $request);
			$testCase->assertInstanceOf('PSX\Http\Response', $response);

		});
	}
}
