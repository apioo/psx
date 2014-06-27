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

		$this->responseFilter->expects($this->once())
			->method('apply')
			->will($this->returnValue('bar'));

		$this->assertEquals('foo', $this->validate->apply('foo', Validate::TYPE_STRING));
		$this->assertEquals('foo', $this->validate->apply('foo', Validate::TYPE_STRING, array()));
		$this->assertEquals('foo', $this->validate->apply('foo', Validate::TYPE_STRING, array($this->successFilter)));
		$this->assertEquals('bar', $this->validate->apply('foo', Validate::TYPE_STRING, array($this->responseFilter)));
	}

	/**
	 * @expectedException PSX\Validate\ValidationException
	 */
	public function testApplyFailure()
	{
		$this->failureFilter->expects($this->once())
			->method('apply')
			->will($this->returnValue(false));

		$this->assertEquals(false, $this->validate->apply('foo', Validate::TYPE_STRING, array($this->failureFilter)));
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

	/**
	 * @expectedException PSX\Validate\ValidationException
	 */
	public function testApplyRequired()
	{
		$this->failureFilter->expects($this->once())
			->method('apply')
			->will($this->returnValue(false));

		$this->assertEquals(false, $this->validate->apply('foo', Validate::TYPE_STRING, array($this->failureFilter), 'bar', true));

	}

	public function testApplyOptional()
	{
		$this->failureFilter->expects($this->once())
			->method('apply')
			->will($this->returnValue(false));

		$this->assertEquals(null, $this->validate->apply('foo', Validate::TYPE_STRING, array($this->failureFilter), 'bar', false));
	}

	public function testValidateTitleNotSet()
	{
		$result = $this->validate->validate(null);

		$this->assertEquals(null, $result->getValue());
		$this->assertEquals('The field "Unknown" is not set', $result->getFirstError());

		$result = $this->validate->validate(null, Validate::TYPE_STRING, array(), 'foo');

		$this->assertEquals(null, $result->getValue());
		$this->assertEquals('The field "foo" is not set', $result->getFirstError());

	}
}
