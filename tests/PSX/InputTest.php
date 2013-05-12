<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

use PSX\Input;
use PSX\Filter\Alnum;

/**
 * InputTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class InputTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function getInput()
	{
		return new Input\Get();
	}

	public function testValidatorError()
	{
		$input = $this->getInput();

		$this->assertEquals(false, $input->foo('foo'));
		$this->assertEquals(1, count($input->getValidator()->getError()));
	}

	public function testReturnValue()
	{
		$input = $this->getInput();

		$this->assertEquals('test', $input->foo('string', array(new Alnum()), 'foo', 'Foo', true, 'test'));
		$this->assertEquals(false, $input->foo('string', array(new Alnum()), 'foo', 'Foo', true));

		$input->offsetSet('foo', 'bar');

		$this->assertEquals('bar', $input->foo('string', array(new Alnum()), 'foo', 'Foo', true, 'test'));
		$this->assertEquals('bar', $input->foo('string', array(new Alnum()), 'foo', 'Foo', true));
	}

	public function testRequired()
	{
		$input = $this->getInput();

		$this->assertEquals(false, $input->bar('string', array(new Alnum()), 'foo', 'Foo', true));
		$this->assertEquals(false, $input->bar('string', array(new Alnum()), 'foo', 'Foo', false));
		$this->assertEquals(1, count($input->getValidator()->getError()));
	}

	public function testCallParameters()
	{
		$input = $this->getInput();

		$input->offsetUnset('foo');

		$this->assertEquals(false, $input->offsetExists('foo'));
		$this->assertEquals(false, $input->foo);
		$this->assertEquals(false, $input['foo']);
		$this->assertEquals(false, $input->foo());
		$this->assertEquals(false, $input->foo('string'));

		$input->offsetSet('foo', 'bar');

		$this->assertEquals(true, $input->offsetExists('foo'));
		$this->assertEquals('bar', $input->foo);
		$this->assertEquals('bar', $input['foo']);
		$this->assertEquals(true, isset($input['foo']));
		$this->assertEquals('bar', $input->foo());
		$this->assertEquals('bar', $input->foo('string'));
	}
}
