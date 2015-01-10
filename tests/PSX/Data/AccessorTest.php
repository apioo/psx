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

use PSX\Validate;
use PSX\Filter;

/**
 * AccessorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class AccessorTest extends \PHPUnit_Framework_TestCase
{
	public function testGet()
	{
		$source = array(
			'foo' => 'bar',
			'bar' => array(
				'foo' => '1',
			),
			'tes' => array(
				array(
					'foo' => 'bar'
				),
			),
		);

		$accessor = new Accessor(new Validate(), $source);

		$this->assertEquals($source, $accessor->getSource());
		$this->assertEquals('bar', $accessor->get('foo'));
		$this->assertEquals(1, $accessor->get('bar.foo'));
		$this->assertEquals('bar', $accessor->get('tes.0.foo'));
	}

	public function testGetFilter()
	{
		$source = array(
			'foo' => 'bar',
			'bar' => array(
				'foo' => '1',
			),
			'tes' => array(
				array(
					'foo' => 'bar'
				),
			),
		);

		$filter = new Filter\Length(3, 8);

		$validate = $this->getMockBuilder('PSX\Validate')
			->setMethods(array('apply'))
			->getMock();

		$validate->expects($this->at(0))
			->method('apply')
			->with($this->equalTo('bar'), $this->equalTo(Validate::TYPE_STRING), $this->equalTo(array()));

		$validate->expects($this->at(1))
			->method('apply')
			->with($this->equalTo(1), $this->equalTo(Validate::TYPE_INTEGER), $this->equalTo(array()));

		$validate->expects($this->at(2))
			->method('apply')
			->with($this->equalTo('bar'), $this->equalTo(Validate::TYPE_STRING), $this->equalTo(array($filter)));

		$accessor = new Accessor($validate, $source);

		$accessor->get('foo');
		$accessor->get('bar.foo', Validate::TYPE_INTEGER);
		$accessor->get('tes.0.foo', Validate::TYPE_STRING, array($filter));
	}

	/**
	 * @expectedException PSX\Validate\ValidationException
	 */
	public function testGetUnknownKey()
	{
		$source = array(
			'bar' => array(
				'foo' => '1',
			),
		);

		$accessor = new Accessor(new Validate(), $source);
		$accessor->get('bar.bar');
	}
}
