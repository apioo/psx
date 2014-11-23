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

namespace PSX\Data;

/**
 * WriterFactoryTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class WriterFactoryTest extends \PHPUnit_Framework_TestCase
{
	protected $writerFactory;

	public function setUp()
	{
		$template = $this->getMockBuilder('PSX\TemplateInterface')
			->getMock();

		$reverseRouter = $this->getMockBuilder('PSX\Loader\ReverseRouter')
			->disableOriginalConstructor()
			->getMock();

		$this->writerFactory = new WriterFactory();
		$this->writerFactory->addWriter(new Writer\Json());
		$this->writerFactory->addWriter(new Writer\Html($template, $reverseRouter));
		$this->writerFactory->addWriter(new Writer\Atom());
		$this->writerFactory->addWriter(new Writer\Form());
		$this->writerFactory->addWriter(new Writer\Jsonp());
		$this->writerFactory->addWriter(new Writer\Soap('http://phpsx.org/2014/data'));
		$this->writerFactory->addWriter(new Writer\Xml());
	}

	public function testGetDefaultWriter()
	{
		$this->assertInstanceOf('PSX\Data\Writer\Json', $this->writerFactory->getDefaultWriter());
	}

	public function testGetWriterByContentType()
	{
		$this->assertInstanceOf('PSX\Data\Writer\Json', $this->writerFactory->getWriterByContentType('application/json'));
		$this->assertInstanceOf('PSX\Data\Writer\Html', $this->writerFactory->getWriterByContentType('text/html'));
		$this->assertInstanceOf('PSX\Data\Writer\Atom', $this->writerFactory->getWriterByContentType('application/atom+xml'));
		$this->assertInstanceOf('PSX\Data\Writer\Form', $this->writerFactory->getWriterByContentType('application/x-www-form-urlencoded'));
		$this->assertInstanceOf('PSX\Data\Writer\Jsonp', $this->writerFactory->getWriterByContentType('application/javascript'));
		$this->assertInstanceOf('PSX\Data\Writer\Soap', $this->writerFactory->getWriterByContentType('application/soap+xml'));
		$this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByContentType('application/xml'));
	}

	public function testGetWriterByContentTypeSupportedWriter()
	{
		$contentType = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';

		$this->assertInstanceOf('PSX\Data\Writer\Html', $this->writerFactory->getWriterByContentType($contentType));
		$this->assertInstanceOf('PSX\Data\Writer\Html', $this->writerFactory->getWriterByContentType($contentType, array('PSX\Data\Writer\Html')));
		$this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByContentType($contentType, array('PSX\Data\Writer\Xml')));
		$this->assertEquals(null, $this->writerFactory->getWriterByContentType($contentType, array('PSX\Data\Writer\Json')));
	}

	public function testGetWriterByContentTypeOrder()
	{
		$supportedWriter = array('PSX\Data\Writer\Html', 'PSX\Data\Writer\Xml');

		$contentType = 'application/json,text/html,application/xml';

		$this->assertInstanceOf('PSX\Data\Writer\Html', $this->writerFactory->getWriterByContentType($contentType, $supportedWriter));

		$contentType = 'application/json,application/xml,text/html';

		$this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByContentType($contentType, $supportedWriter));
	}

	public function testGetWriterByFormat()
	{
		$this->assertInstanceOf('PSX\Data\Writer\Json', $this->writerFactory->getWriterByFormat('json'));
		$this->assertInstanceOf('PSX\Data\Writer\Html', $this->writerFactory->getWriterByFormat('html'));
		$this->assertInstanceOf('PSX\Data\Writer\Atom', $this->writerFactory->getWriterByFormat('atom'));
		$this->assertInstanceOf('PSX\Data\Writer\Form', $this->writerFactory->getWriterByFormat('form'));
		$this->assertInstanceOf('PSX\Data\Writer\Jsonp', $this->writerFactory->getWriterByFormat('jsonp'));
		$this->assertInstanceOf('PSX\Data\Writer\Soap', $this->writerFactory->getWriterByFormat('soap'));
		$this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByFormat('xml'));	
	}

	public function testGetWriterByInstance()
	{
		$this->assertInstanceOf('PSX\Data\Writer\Json', $this->writerFactory->getWriterByInstance('PSX\Data\Writer\Json'));
		$this->assertInstanceOf('PSX\Data\Writer\Html', $this->writerFactory->getWriterByInstance('PSX\Data\Writer\Html'));
		$this->assertInstanceOf('PSX\Data\Writer\Atom', $this->writerFactory->getWriterByInstance('PSX\Data\Writer\Atom'));
		$this->assertInstanceOf('PSX\Data\Writer\Form', $this->writerFactory->getWriterByInstance('PSX\Data\Writer\Form'));
		$this->assertInstanceOf('PSX\Data\Writer\Jsonp', $this->writerFactory->getWriterByInstance('PSX\Data\Writer\Jsonp'));
		$this->assertInstanceOf('PSX\Data\Writer\Soap', $this->writerFactory->getWriterByInstance('PSX\Data\Writer\Soap'));
		$this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByInstance('PSX\Data\Writer\Xml'));		
	}

	public function testContentNegotiationExplicit()
	{
		$this->writerFactory->setContentNegotiation('text/plain', WriterInterface::XML);

		$this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByContentType('text/plain'));
	}

	public function testContentNegotiationWildcardSubtype()
	{
		$this->writerFactory->setContentNegotiation('text/*', WriterInterface::XML);

		$this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByContentType('text/plain'));
		$this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByContentType('text/foo'));
		$this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByContentType('application/xml'));
		$this->assertInstanceOf('PSX\Data\Writer\Json', $this->writerFactory->getWriterByContentType('application/json'));
		$this->assertNull($this->writerFactory->getWriterByContentType('image/png'));
	}

	public function testContentNegotiationAll()
	{
		$this->writerFactory->setContentNegotiation('*/*', WriterInterface::XML);

		$this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByContentType('text/plain'));
		$this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByContentType('application/json'));
	}

	public function testContentNegotiation()
	{
		$this->writerFactory->setContentNegotiation('image/*', WriterInterface::HTML);

		$this->assertInstanceOf('PSX\Data\Writer\Html', $this->writerFactory->getWriterByContentType('image/webp,*/*;q=0.8'));
		$this->assertInstanceOf('PSX\Data\Writer\Html', $this->writerFactory->getWriterByContentType('image/png, image/svg+xml, image/*;q=0.8, */*;q=0.5'));
		$this->assertInstanceOf('PSX\Data\Writer\Html', $this->writerFactory->getWriterByContentType('text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'));
	}
}
