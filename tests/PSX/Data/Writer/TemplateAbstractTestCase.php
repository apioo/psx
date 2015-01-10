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

namespace PSX\Data\Writer;

use PSX\Data\WriterTestCase;
use PSX\TemplateInterface;
use PSX\Loader\ReverseRouter;

/**
 * TemplateAbstractTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class TemplateAbstractTestCase extends WriterTestCase
{
	/**
	 * Returns the writer
	 *
	 * @return PSX\Data\WriterInterface
	 */
	abstract protected function getWriter(TemplateInterface $template, ReverseRouter $router);

	public function testWrite()
	{
		$template = $this->getMock('PSX\TemplateInterface');
		$router   = $this->getMockBuilder('PSX\Loader\ReverseRouter')
			->disableOriginalConstructor()
			->getMock();

		$template->expects($this->at(3))
				->method('assign')
				->with($this->equalTo('self'));

		$template->expects($this->at(4))
				->method('assign')
				->with($this->equalTo('url'));

		$template->expects($this->at(5))
				->method('assign')
				->with($this->equalTo('base'));

		$template->expects($this->at(6))
				->method('assign')
				->with($this->equalTo('render'));

		$template->expects($this->at(7))
				->method('assign')
				->with($this->equalTo('location'));

		$template->expects($this->at(8))
				->method('assign')
				->with($this->equalTo('router'), $this->identicalTo($router));

		$template->expects($this->at(9))
				->method('assign')
				->with($this->equalTo('id'), $this->equalTo(1));

		$template->expects($this->at(10))
				->method('assign')
				->with($this->equalTo('author'), $this->equalTo('foo'));

		$template->expects($this->at(11))
				->method('assign')
				->with($this->equalTo('title'), $this->equalTo('bar'));

		$template->expects($this->at(12))
				->method('assign')
				->with($this->equalTo('content'), $this->equalTo('foobar'));

		$template->expects($this->at(13))
				->method('assign')
				->with($this->equalTo('date'));

		$template->expects($this->once())
				->method('transform')
				->will($this->returnValue('foo'));

		$writer = $this->getWriter($template, $router);
		$actual = $writer->write($this->getRecord());

		$expect = <<<TEXT
foo
TEXT;

		$this->assertEquals($expect, $actual);
	}

	public function testWriteResultSet()
	{
		$template = $this->getMock('PSX\TemplateInterface');
		$router   = $this->getMockBuilder('PSX\Loader\ReverseRouter')
			->disableOriginalConstructor()
			->getMock();

		$template->expects($this->at(3))
				->method('assign')
				->with($this->equalTo('self'));

		$template->expects($this->at(4))
				->method('assign')
				->with($this->equalTo('url'));

		$template->expects($this->at(5))
				->method('assign')
				->with($this->equalTo('base'));

		$template->expects($this->at(6))
				->method('assign')
				->with($this->equalTo('render'));

		$template->expects($this->at(7))
				->method('assign')
				->with($this->equalTo('location'));

		$template->expects($this->at(8))
				->method('assign')
				->with($this->equalTo('router'), $this->identicalTo($router));

		$template->expects($this->at(9))
				->method('assign')
				->with($this->equalTo('totalResults'), $this->equalTo(2));

		$template->expects($this->at(10))
				->method('assign')
				->with($this->equalTo('startIndex'), $this->equalTo(0));

		$template->expects($this->at(11))
				->method('assign')
				->with($this->equalTo('itemsPerPage'), $this->equalTo(8));

		$template->expects($this->at(12))
				->method('assign')
				->with($this->equalTo('entry'));

		$template->expects($this->once())
				->method('transform')
				->will($this->returnValue('foo'));

		$writer = $this->getWriter($template, $router);
		$actual = $writer->write($this->getResultSet());

		$expect = <<<TEXT
foo
TEXT;

		$this->assertEquals($expect, $actual);
	}

	/**
	 * When no template was set we get the template from the controller class 
	 * name
	 */
	public function testAutomaticTemplateDetection()
	{
		$template = $this->getMock('PSX\TemplateInterface');
		$router   = $this->getMockBuilder('PSX\Loader\ReverseRouter')
			->disableOriginalConstructor()
			->getMock();

		$template->expects($this->at(0))
				->method('hasFile')
				->will($this->returnValue(false));

		$template->expects($this->at(1))
				->method('setDir')
				->with($this->equalTo('library/Foo/Resource'));

		$writer = $this->getWriter($template, $router);
		$writer->setControllerClass('Foo\Application\News\DetailDescription');

		$template->expects($this->at(2))
				->method('set')
				->with($this->equalTo('news/detail_description.' . $writer->getFileExtension()));

		$actual = $writer->write($this->getRecord());
	}

	/**
	 * If a template file was set but the file doesnt actually exist we use the
	 * fitting dir from the controller class name
	 */
	public function testSetNotExistingTemplateFile()
	{
		$template = $this->getMock('PSX\TemplateInterface');
		$router   = $this->getMockBuilder('PSX\Loader\ReverseRouter')
			->disableOriginalConstructor()
			->getMock();

		$template->expects($this->at(0))
				->method('hasFile')
				->will($this->returnValue(true));

		$template->expects($this->at(1))
				->method('fileExists')
				->will($this->returnValue(false));

		$template->expects($this->at(2))
				->method('setDir')
				->with($this->equalTo('library/Foo/Resource'));

		$writer = $this->getWriter($template, $router);
		$writer->setControllerClass('Foo\Application\News\DetailDescription');

		$actual = $writer->write($this->getRecord());
	}

	/**
	 * If a template file was set which exists we simply use this file and dont 
	 * set any dir
	 */
	public function testSetExistingTemplateFile()
	{
		$template = $this->getMock('PSX\TemplateInterface');
		$router   = $this->getMockBuilder('PSX\Loader\ReverseRouter')
			->disableOriginalConstructor()
			->getMock();

		$template->expects($this->at(0))
				->method('hasFile')
				->will($this->returnValue(true));

		$template->expects($this->at(1))
				->method('fileExists')
				->will($this->returnValue(true));

		$template->expects($this->at(2))
				->method('setDir')
				->with($this->equalTo(null));

		$writer = $this->getWriter($template, $router);
		$writer->setControllerClass('Foo\Application\News\DetailDescription');

		$actual = $writer->write($this->getRecord());
	}
}
