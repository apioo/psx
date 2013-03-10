<?php
/*
 *  $Id: ValidateTest.php 559 2012-07-29 02:39:55Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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
 * PSX_ValidateTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 559 $
 */
class PSX_ValidateTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testApply()
	{
		$validate = new Validate();

		$this->assertEquals('foo', $validate->apply('foo', 'string', array()));
		$this->assertEquals(false, $validate->apply('foo', 'string', array(new InArray(array('test')))));
		$this->assertEquals(false, $validate->apply('foo', 'string', array(new InArray(array('test'))), 'bar'));
		$this->assertEquals(false, $validate->apply('foo', 'string', array(new InArray(array('test'))), 'bar', 'Bar'));

	}

	public function testApplyScalar()
	{
		$validate = new Validate();

		$this->assertEquals('foo', $validate->apply('foo', 'string'));
		$this->assertEquals(0, $validate->apply('foo', 'integer'));
		$this->assertEquals(0.0, $validate->apply('foo', 'float'));
		$this->assertEquals(true, $validate->apply('foo', 'boolean'));
	}

	public function testApplyRequired()
	{
		$validate = new Validate();

		$this->assertEquals(false, $validate->apply('foo', 'string', array(new InArray(array('test'))), 'bar', 'Bar', false));
		$this->assertEquals(false, $validate->hasError());
		$this->assertEquals(false, $validate->apply('foo', 'string', array(new InArray(array('test'))), 'bar', 'Bar', true));
		$this->assertEquals(true, $validate->hasError());
	}

	public function testApplyReturnValue()
	{
		$validate = new Validate();

		$this->assertEquals('foo', $validate->apply('foo', 'string', array(), 'bar', 'Bar', true, 'test'));
		$this->assertEquals('test', $validate->apply('foo', 'string', array(new InArray(array('test'))), 'bar', 'Bar', true, 'test'));
	}

	public function testAddError()
	{
		$validate = new Validate();
		$validate->addError('foo', 'bar');

		$this->assertEquals(array('foo' => 'bar'), $validate->getError());
	}

	public function testHasError()
	{
		$validate = new Validate();

		$this->assertEquals(false, $validate->hasError());

		$validate->addError('foo', 'bar');

		$this->assertEquals(true, $validate->hasError());
	}

	public function testGetError()
	{
		$validate = new Validate();
		$validate->addError('foo', 'bar');

		$this->assertEquals(array('foo' => 'bar'), $validate->getError());
	}

	public function testGetLastError()
	{
		$validate = new Validate();
		$validate->addError('foo', 'bar');
		$validate->addError('bar', 'foo');

		$this->assertEquals('foo', $validate->getLastError());
	}
}
