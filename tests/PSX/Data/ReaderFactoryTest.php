<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Data;

/**
 * ReaderFactoryTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
