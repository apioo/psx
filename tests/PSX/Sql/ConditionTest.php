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

namespace PSX\Sql;

/**
 * ConditionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ConditionTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testCondition()
	{
		$con = new Condition(array('id', '=', '1'));

		$this->assertEquals('WHERE id = ?', $con->getStatment());
		$this->assertEquals(array('1'), $con->getValues());
		$this->assertEquals(true, $con->hasCondition());


		$con = new Condition();
		$con->add('id', '=', '1');

		$this->assertEquals('WHERE id = ?', $con->getStatment());
		$this->assertEquals(array('1'), $con->getValues());
		$this->assertEquals(true, $con->hasCondition());
	}

	public function testConditionMultiple()
	{
		$con = new Condition();
		$con->add('id', '=', '1');
		$con->add('id', '=', '2');

		$this->assertEquals('WHERE id = ? AND id = ?', $con->getStatment());
		$this->assertEquals(array('1', '2'), $con->getValues());
		$this->assertEquals(true, $con->hasCondition());


		$con = new Condition();
		$con->add('id', '=', '1', 'OR');
		$con->add('id', '=', '2');

		$this->assertEquals('WHERE id = ? OR id = ?', $con->getStatment());
		$this->assertEquals(array('1', '2'), $con->getValues());
		$this->assertEquals(true, $con->hasCondition());
	}

	public function testAdd()
	{
		$con = new Condition();
		$con->add('id', '=', '1');

		$this->assertEquals('WHERE id = ?', $con->getStatment());
		$this->assertEquals(array('1'), $con->getValues());
		$this->assertEquals(true, $con->hasCondition());
	}

	public function testCount()
	{
		$con = new Condition();
		$con->add('id', '=', '1');
		$con->add('id', '=', '2');
		$con->add('id', '=', '3');

		$this->assertEquals(3, count($con));
	}

	public function testMerge()
	{
		$con_1 = new Condition(array('id', '=', '1'));
		$con_2 = new Condition(array('id', '=', '2'));

		$this->assertEquals(true, $con_1->hasCondition());

		$con_1->merge($con_2);

		$this->assertEquals('WHERE id = ? AND id = ?', $con_1->getStatment());
		$this->assertEquals(array('1', '2'), $con_1->getValues());
		$this->assertEquals(true, $con_1->hasCondition());
	}

	public function testRemove()
	{
		$con = new Condition();
		$con->add('id', '=', '1');
		$con->add('title', '=', '2');
		$con->add('date', '=', '3');

		$this->assertEquals(3, count($con));

		$con->remove('title');

		$this->assertEquals(2, count($con));
	}

	public function testRemoveAll()
	{
		$con = new Condition(array('id', '=', '1'));

		$this->assertEquals('WHERE id = ?', $con->getStatment());
		$this->assertEquals(array('1'), $con->getValues());
		$this->assertEquals(true, $con->hasCondition());

		$con->removeAll();

		$this->assertEquals('', $con->getStatment());
		$this->assertEquals(array(), $con->getValues());
		$this->assertEquals(false, $con->hasCondition());
	}

	public function testToArray()
	{
		$con = new Condition();
		$con->add('id', '=', '1');

		$this->assertEquals(array(array(

			Condition::COLUMN      => 'id',
			Condition::OPERATOR    => '=',
			Condition::VALUE       => '1',
			Condition::CONJUNCTION => 'AND',
			Condition::TYPE        => Condition::TYPE_SCALAR,

		)), $con->toArray());
	}
}
