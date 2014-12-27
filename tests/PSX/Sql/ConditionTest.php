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
		$con = new Condition(array('id', '=', '1', 'OR'));
		$con->add('id', '=', '2');

		$this->assertEquals('WHERE id = ? OR id = ?', $con->getStatment());
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
		$con->add('foo', 'IN', 'foo');
		$con->add('foo', 'IN', array(1, 2));

		$this->assertEquals('WHERE id = ? AND foo IN (?) AND foo IN (?,?)', $con->getStatment());
		$this->assertEquals(array('1', 'foo', 1, 2), $con->getValues());
		$this->assertEquals(true, $con->hasCondition());
	}

	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testAddInvalidOperator()
	{
		$con = new Condition();
		$con->add('id', 'foo', '1');
	}

	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testAddInvalidConjunction()
	{
		$con = new Condition();
		$con->add('id', '=', '1', 'foo');
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

	public function testRemoveInvalidColumn()
	{
		$con = new Condition();
		$con->add('id', '=', '1');

		$this->assertEquals(1, count($con));

		$con->remove('title');

		$this->assertEquals(1, count($con));
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

		$this->assertEquals(array(
			array(
				Condition::COLUMN      => 'id',
				Condition::OPERATOR    => '=',
				Condition::VALUE       => '1',
				Condition::CONJUNCTION => 'AND',
				Condition::TYPE        => Condition::TYPE_SCALAR,
			)
		), $con->toArray());
	}

	public function testGetStatment()
	{
		$con = Condition::fromCriteria(array(
			'foo' => 'bar',
			'bar' => array(1, 2),
			'baz' => null,
		));

		$this->assertEquals('WHERE foo = ? AND bar IN (?,?) AND baz IS NULL', $con->getStatment());

		// test buffer
		$this->assertEquals('WHERE foo = ? AND bar IN (?,?) AND baz IS NULL', $con->getStatment());
	}

	public function testGetValues()
	{
		$con = Condition::fromCriteria(array(
			'foo' => 'bar',
			'bar' => array(1, 2),
			'baz' => null,
		));

		$this->assertEquals(array('bar', 1, 2), $con->getValues());
	}

	public function testGetArray()
	{
		$criteria = array(
			'foo' => 'bar',
			'bar' => array(1, 2),
			'baz' => null,
		);

		$con = Condition::fromCriteria($criteria);

		// null converts to the string NULL
		$criteria['baz'] = 'NULL';

		$this->assertEquals($criteria, $con->getArray());
	}

	public function testToString()
	{
		$con = Condition::fromCriteria(array(
			'foo' => 'bar',
			'bar' => array(1, 2),
			'baz' => null,
		));

		$this->assertEquals('c2f74e822ac583a9face16c2460574c4', $con->toString());
		$this->assertEquals('c2f74e822ac583a9face16c2460574c4', (string) $con);
	}

	public function testFromCriteria()
	{
		$con = Condition::fromCriteria(array(
			'foo' => 'bar',
			'bar' => array(1, 2),
			'baz' => null,
		));

		$this->assertEquals(array(
			array(
				Condition::COLUMN      => 'foo',
				Condition::OPERATOR    => '=',
				Condition::VALUE       => 'bar',
				Condition::CONJUNCTION => 'AND',
				Condition::TYPE        => Condition::TYPE_SCALAR,
			),
			array(
				Condition::COLUMN      => 'bar',
				Condition::OPERATOR    => 'IN',
				Condition::VALUE       => array(1, 2),
				Condition::CONJUNCTION => 'AND',
				Condition::TYPE        => Condition::TYPE_IN,
			),
			array(
				Condition::COLUMN      => 'baz',
				Condition::OPERATOR    => 'IS',
				Condition::VALUE       => 'NULL',
				Condition::CONJUNCTION => 'AND',
				Condition::TYPE        => Condition::TYPE_RAW,
			),
		), $con->toArray());
	}
}
