<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Sql\Tests;

use PSX\Sql\Condition;

/**
 * ConditionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ConditionTest extends \PHPUnit_Framework_TestCase
{
    public function testCondition()
    {
        $con = new Condition(array('id', '=', '1'));

        $this->assertEquals('WHERE (id = ?)', $con->getStatment());
        $this->assertEquals(array('1'), $con->getValues());

        $con = new Condition();
        $con->add('id', '=', '1');

        $this->assertEquals('WHERE (id = ?)', $con->getStatment());
        $this->assertEquals(array('1'), $con->getValues());
    }

    public function testConditionMultiple()
    {
        $con = new Condition(array('id', '=', '1', 'OR'));
        $con->add('id', '=', '2');

        $this->assertEquals('WHERE (id = ? OR id = ?)', $con->getStatment());
        $this->assertEquals(array('1', '2'), $con->getValues());

        $con2 = new Condition();
        $con2->add('id', '=', '1');
        $con2->add('id', '=', '2');
        $con2->addExpression($con);

        $this->assertEquals('WHERE (id = ? AND id = ? AND (id = ? OR id = ?))', $con2->getStatment());
        $this->assertEquals(array('1', '2', '1', '2'), $con2->getValues());
    }

    public function testAdd()
    {
        $con = new Condition();
        $con->add('id', '=', '1');
        $con->add('foo', 'IN', array(1, 2));

        $this->assertEquals('WHERE (id = ? AND foo IN (?,?))', $con->getStatment());
        $this->assertEquals(array('1', 1, 2), $con->getValues());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddInvalidOperator()
    {
        $con = new Condition();
        $con->add('id', 'foo', '1');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddInvalidConjunction()
    {
        $con = new Condition();
        $con->add('id', '=', '1', 'foo');
    }

    public function testEquals()
    {
        $con = new Condition();
        $con->equals('foo', 2);

        $this->assertEquals('WHERE (foo = ?)', $con->getStatment());
        $this->assertEquals([2], $con->getValues());
    }

    public function testNotEquals()
    {
        $con = new Condition();
        $con->notEquals('foo', 2);

        $this->assertEquals('WHERE (foo != ?)', $con->getStatment());
        $this->assertEquals([2], $con->getValues());
    }

    public function testGreater()
    {
        $con = new Condition();
        $con->greater('foo', 2);

        $this->assertEquals('WHERE (foo > ?)', $con->getStatment());
        $this->assertEquals([2], $con->getValues());
    }

    public function testGreaterThen()
    {
        $con = new Condition();
        $con->greaterThen('foo', 2);

        $this->assertEquals('WHERE (foo >= ?)', $con->getStatment());
        $this->assertEquals([2], $con->getValues());
    }

    public function testLower()
    {
        $con = new Condition();
        $con->lower('foo', 2);

        $this->assertEquals('WHERE (foo < ?)', $con->getStatment());
        $this->assertEquals([2], $con->getValues());
    }

    public function testLowerThen()
    {
        $con = new Condition();
        $con->lowerThen('foo', 2);

        $this->assertEquals('WHERE (foo <= ?)', $con->getStatment());
        $this->assertEquals([2], $con->getValues());
    }

    public function testLike()
    {
        $con = new Condition();
        $con->like('foo', 'bar');

        $this->assertEquals('WHERE (foo LIKE ?)', $con->getStatment());
        $this->assertEquals(['bar'], $con->getValues());
    }

    public function testNotLike()
    {
        $con = new Condition();
        $con->notLike('foo', 'bar');

        $this->assertEquals('WHERE (foo NOT LIKE ?)', $con->getStatment());
        $this->assertEquals(['bar'], $con->getValues());
    }

    public function testBetween()
    {
        $con = new Condition();
        $con->between('id', 8, 16);

        $this->assertEquals('WHERE (id BETWEEN ? AND ?)', $con->getStatment());
        $this->assertEquals([8, 16], $con->getValues());
    }

    public function testIn()
    {
        $con = new Condition();
        $con->in('id', [8, 16]);

        $this->assertEquals('WHERE (id IN (?,?))', $con->getStatment());
        $this->assertEquals([8, 16], $con->getValues());
    }

    public function testNil()
    {
        $con = new Condition();
        $con->nil('foo');

        $this->assertEquals('WHERE (foo IS NULL)', $con->getStatment());
        $this->assertEquals([], $con->getValues());
    }

    public function testNotNil()
    {
        $con = new Condition();
        $con->notNil('foo');

        $this->assertEquals('WHERE (foo IS NOT NULL)', $con->getStatment());
        $this->assertEquals([], $con->getValues());
    }

    public function testRaw()
    {
        $con = new Condition();
        $con->raw('foo IN (SELECT id FROM foo WHERE id = ?)', [1]);

        $this->assertEquals('WHERE (foo IN (SELECT id FROM foo WHERE id = ?))', $con->getStatment());
        $this->assertEquals([1], $con->getValues());
    }

    public function testRegexp()
    {
        $con = new Condition();
        $con->regexp('foo', '[A-z]+');

        $this->assertEquals('WHERE (foo RLIKE ?)', $con->getStatment());
        $this->assertEquals(['[A-z]+'], $con->getValues());
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

        $this->assertEquals('WHERE (id = ? AND id = ?)', $con_1->getStatment());
        $this->assertEquals(array('1', '2'), $con_1->getValues());
        $this->assertEquals(true, $con_1->hasCondition());
    }

    public function testGet()
    {
        $con = new Condition();
        $con->add('title', '=', '2');

        $titleExpression = $con->get('title');

        $this->assertInstanceOf('PSX\Sql\Condition\ExpressionInterface', $titleExpression);
        $this->assertEquals(['2'], $titleExpression->getValues());

        $this->assertNull($con->get('foo'));
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

        $this->assertEquals('WHERE (id = ?)', $con->getStatment());
        $this->assertEquals(array('1'), $con->getValues());
        $this->assertEquals(true, $con->hasCondition());

        $con->removeAll();

        $this->assertEquals('WHERE 1 = 1', $con->getStatment());
        $this->assertEquals(array(), $con->getValues());
        $this->assertEquals(false, $con->hasCondition());
    }

    public function testGetStatment()
    {
        $con = Condition::fromCriteria(array(
            'foo' => 'bar',
            'bar' => array(1, 2),
            'baz' => null,
        ));

        $this->assertEquals('WHERE (foo = ? AND bar IN (?,?) AND baz IS NULL)', $con->getStatment());
        $this->assertEquals('WHERE (foo = ? AND bar IN (?,?) AND baz IS NULL)', $con->getStatment());
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

        $this->assertEquals($criteria, $con->getArray());
    }

    public function testFromCriteria()
    {
        $con = Condition::fromCriteria(array(
            'foo' => 'bar',
            'bar' => array(1, 2),
            'baz' => null,
        ));

        $result = $con->toArray();

        $this->assertContainsOnlyInstancesOf('PSX\Sql\Condition\ExpressionInterface', $result);
        $this->assertEquals('WHERE (foo = ? AND bar IN (?,?) AND baz IS NULL)', $con->getStatment());
    }
}
