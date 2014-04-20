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

namespace PSX;

use PSX\Filter\InArray;

/**
 * ValidateTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ValidateTest extends \PHPUnit_Framework_TestCase
{
	protected $successFilter;
	protected $failureFilter;
	protected $responseFilter;
	protected $validate;

	protected function setUp()
	{
		$this->successFilter = $this->getMockBuilder('PSX\FilterAbstract')
			->getMock();

		$this->failureFilter = $this->getMockBuilder('PSX\FilterAbstract')
			->getMock();

		$this->responseFilter = $this->getMockBuilder('PSX\FilterAbstract')
			->getMock();

		$this->validate = new Validate();
	}

	protected function tearDown()
	{
	}

	public function testApply()
	{
		$this->successFilter->expects($this->once())
			->method('apply')
			->will($this->returnValue(true));

		$this->failureFilter->expects($this->once())
			->method('apply')
			->will($this->returnValue(false));

		$this->responseFilter->expects($this->once())
			->method('apply')
			->will($this->returnValue('bar'));

		$this->assertEquals('foo', $this->validate->apply('foo', Validate::TYPE_STRING));
		$this->assertEquals('foo', $this->validate->apply('foo', Validate::TYPE_STRING, array()));
		$this->assertEquals('foo', $this->validate->apply('foo', Validate::TYPE_STRING, array($this->successFilter)));
		$this->assertEquals(false, $this->validate->apply('foo', Validate::TYPE_STRING, array($this->failureFilter)));
		$this->assertEquals('bar', $this->validate->apply('foo', Validate::TYPE_STRING, array($this->responseFilter)));
	}

	public function testApplyScalar()
	{
		$this->assertEquals('foo', $this->validate->apply('foo', Validate::TYPE_STRING));
		$this->assertInternalType(Validate::TYPE_STRING, $this->validate->apply('foo', Validate::TYPE_STRING));

		$this->assertEquals(0, $this->validate->apply('foo', Validate::TYPE_INTEGER));
		$this->assertInternalType(Validate::TYPE_INTEGER, $this->validate->apply('foo', Validate::TYPE_INTEGER));

		$this->assertEquals(0.0, $this->validate->apply('foo', Validate::TYPE_FLOAT));
		$this->assertInternalType(Validate::TYPE_FLOAT, $this->validate->apply('foo', Validate::TYPE_FLOAT));

		$this->assertEquals(true, $this->validate->apply('foo', Validate::TYPE_BOOLEAN));
		$this->assertInternalType(Validate::TYPE_BOOLEAN, $this->validate->apply('foo', Validate::TYPE_BOOLEAN));
	}

	public function testApplyRequired()
	{
		$this->failureFilter->expects($this->any())
			->method('apply')
			->will($this->returnValue(false));

		$this->assertEquals(false, $this->validate->apply('foo', Validate::TYPE_STRING, array($this->failureFilter), 'bar', 'Bar', false));
		$this->assertFalse($this->validate->hasError());

		$this->assertEquals(false, $this->validate->apply('foo', Validate::TYPE_STRING, array($this->failureFilter), 'bar', 'Bar', true));
		$this->assertTrue($this->validate->hasError());
	}

	public function testApplyReturnValue()
	{
		$this->successFilter->expects($this->once())
			->method('apply')
			->will($this->returnValue(true));

		$this->failureFilter->expects($this->once())
			->method('apply')
			->will($this->returnValue(false));

		$this->assertEquals('foo', $this->validate->apply('foo', Validate::TYPE_STRING, array($this->successFilter), 'bar', 'Bar', true, 'test'));
		$this->assertEquals('test', $this->validate->apply('foo', Validate::TYPE_STRING, array($this->failureFilter), 'bar', 'Bar', true, 'test'));
	}

	public function testApplyAddError()
	{
		$this->failureFilter->expects($this->once())
			->method('apply')
			->will($this->returnValue(false));

		$this->failureFilter->expects($this->once())
			->method('getErrorMsg')
			->will($this->returnValue('%s error occured'));

		$this->assertEquals(false, $this->validate->apply('foo', Validate::TYPE_STRING, array($this->failureFilter)));

		$this->assertTrue($this->validate->hasError());
		$this->assertEquals(array(0 => 'Unknown error occured'), $this->validate->getError());
	}

	public function testApplyAddErrorKey()
	{
		$this->failureFilter->expects($this->once())
			->method('apply')
			->will($this->returnValue(false));

		$this->failureFilter->expects($this->once())
			->method('getErrorMsg')
			->will($this->returnValue('%s error occured'));

		$this->assertEquals(false, $this->validate->apply('foo', Validate::TYPE_STRING, array($this->failureFilter), 'bar'));

		$this->assertTrue($this->validate->hasError());
		$this->assertEquals(array('bar' => 'Bar error occured'), $this->validate->getError());
	}

	public function testApplyAddErrorTitle()
	{
		$this->failureFilter->expects($this->once())
			->method('apply')
			->will($this->returnValue(false));

		$this->failureFilter->expects($this->once())
			->method('getErrorMsg')
			->will($this->returnValue('%s error occured'));

		$this->assertEquals(false, $this->validate->apply('foo', Validate::TYPE_STRING, array($this->failureFilter), 'bar', 'Foo'));

		$this->assertTrue($this->validate->hasError());
		$this->assertEquals(array('bar' => 'Foo error occured'), $this->validate->getError());
	}

	public function testErrorKeys()
	{
		$this->failureFilter->expects($this->any())
			->method('apply')
			->will($this->returnValue(false));

		$this->failureFilter->expects($this->any())
			->method('getErrorMsg')
			->will($this->returnValue('%s error occured'));

		$this->assertEquals(false, $this->validate->apply('foo', Validate::TYPE_STRING, array($this->failureFilter), 'foo'));
		$this->assertEquals('Foo error occured', $this->validate->getLastError());

		$this->assertEquals(false, $this->validate->apply('foo', Validate::TYPE_STRING, array($this->failureFilter), 'foo', 'Bar'));	
		$this->assertEquals('Bar error occured', $this->validate->getLastError());
	}

	public function testAddError()
	{
		$this->assertFalse($this->validate->hasError());

		$this->validate->addError('foo', 'bar');

		$this->assertTrue($this->validate->hasError());
		$this->assertEquals(array('foo' => 'bar'), $this->validate->getError());
	}

	public function testHasError()
	{
		$this->assertFalse($this->validate->hasError());

		$this->validate->addError('foo', 'bar');

		$this->assertTrue($this->validate->hasError());
	}

	public function testGetError()
	{
		$this->validate->addError('foo', 'bar');
		$this->validate->addError('foo', 'bar');
		$this->validate->addError(null, 'bar');
		$this->validate->addError(null, 'bar');

		$this->assertEquals(array('foo' => 'bar', 0 => 'bar', 1 => 'bar'), $this->validate->getError());
	}

	public function testGetLastError()
	{
		$this->validate->addError('foo', 'bar');
		$this->validate->addError('bar', 'foo');

		$this->assertEquals('foo', $this->validate->getLastError());
	}

	public function testClearError()
	{
		$this->validate->addError('foo', 'bar');

		$this->assertEquals(true, $this->validate->hasError());

		$this->validate->clearError();

		$this->assertEquals(false, $this->validate->hasError());
	}
}
