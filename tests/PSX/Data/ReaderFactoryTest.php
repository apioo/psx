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

namespace PSX\Data;

/**
 * ReaderFactoryTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ReaderFactoryTest extends \PHPUnit_Framework_TestCase
{
	protected $readerFactory;

	public function setUp()
	{
		$this->readerFactory = new ReaderFactory();
		$this->readerFactory->addReader(new Reader\Json());
		$this->readerFactory->addReader(new Reader\Form());
		$this->readerFactory->addReader(new Reader\Xml());
	}

	public function testGetDefaultReader()
	{
		$this->assertInstanceOf('PSX\Data\Reader\Json', $this->readerFactory->getDefaultReader());
	}

	public function testGetReaderByContentType()
	{
		$this->assertInstanceOf('PSX\Data\Reader\Json', $this->readerFactory->getReaderByContentType('application/json'));
		$this->assertInstanceOf('PSX\Data\Reader\Form', $this->readerFactory->getReaderByContentType('application/x-www-form-urlencoded'));
		$this->assertInstanceOf('PSX\Data\Reader\Xml', $this->readerFactory->getReaderByContentType('application/xml'));
	}

	public function testGetReaderByContentTypeSupportedReader()
	{
		$supportedReader = array('PSX\Data\Reader\Form', 'PSX\Data\Reader\Xml');
		$contentType     = 'application/xml';

		$this->assertInstanceOf('PSX\Data\Reader\Xml', $this->readerFactory->getReaderByContentType($contentType, $supportedReader));
	}

	public function testGetReaderByInstance()
	{
		$this->assertInstanceOf('PSX\Data\Reader\Json', $this->readerFactory->getReaderByInstance('PSX\Data\Reader\Json'));
		$this->assertInstanceOf('PSX\Data\Reader\Form', $this->readerFactory->getReaderByInstance('PSX\Data\Reader\Form'));
		$this->assertInstanceOf('PSX\Data\Reader\Xml', $this->readerFactory->getReaderByInstance('PSX\Data\Reader\Xml'));
		$this->assertEquals(null, $this->readerFactory->getReaderByInstance('PSX\Data\Reader\Foo'));
	}
}
