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

namespace PSX\Sql;

use Countable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use InvalidArgumentException;
use PSX\Sql\Condition\ExpressionAbstract;
use PSX\Sql\Condition\ExpressionInterface;

/**
 * Condition which represents a SQL expression which filters a result set
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Condition extends ExpressionAbstract implements Countable
{
    private static $arithmeticOperator = array('=', 'IS', '!=', 'IS NOT', 'LIKE', 'NOT LIKE', '<', '>', '<=', '>=', 'IN');
    private static $logicOperator      = array('AND', 'OR', '&&', '||');

    protected $expressions = array();
    protected $isInverse   = false;

    public function __construct(array $condition = array())
    {
        if (count($condition) >= 3) {
            if (isset($condition[3])) {
                $this->add($condition[0], $condition[1], $condition[2], $condition[3]);
            } else {
                $this->add($condition[0], $condition[1], $condition[2]);
            }
        }
    }

    /**
     * Adds an condition and tries to detect the type of the condition based on
     * the provided values. It is recommended to use an explicit method
     *
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @param string $conjunction
     * @return \PSX\Sql\Condition
     */
    public function add($column, $operator, $value, $conjunction = 'AND')
    {
        if (!in_array($operator, self::$arithmeticOperator)) {
            throw new InvalidArgumentException('Invalid arithmetic operator (allowed: ' . implode(', ', self::$arithmeticOperator) . ')');
        }

        if (!in_array($conjunction, self::$logicOperator)) {
            throw new InvalidArgumentException('Invalid logic operator (allowed: ' . implode(', ', self::$logicOperator) . ')');
        }

        if ($operator == 'IN' && is_array($value)) {
            $expr = new Condition\In($column, $value, $conjunction);
        } elseif (($operator == '=' || $operator == 'IS') && $value === null) {
            $expr = new Condition\Nil($column, $conjunction);
        } elseif (($operator == '!=' || $operator == 'IS NOT') && $value === null) {
            $expr = new Condition\NotNil($column, $conjunction);
        } else {
            $expr = new Condition\Basic($column, $operator, $value, $conjunction);
        }

        return $this->addExpression($expr);
    }

    /**
     * Asserts that the column is equals to the value
     *
     * @param string $column
     * @param string $value
     * @param string $conjunction
     * @return \PSX\Sql\Condition
     */
    public function equals($column, $value, $conjunction = 'AND')
    {
        return $this->addExpression(new Condition\Basic($column, '=', $value, $conjunction));
    }

    /**
     * Asserts that the column is not equal to the value
     *
     * @param string $column
     * @param string $value
     * @param string $conjunction
     * @return \PSX\Sql\Condition
     */
    public function notEquals($column, $value, $conjunction = 'AND')
    {
        return $this->addExpression(new Condition\Basic($column, '!=', $value, $conjunction));
    }

    /**
     * Asserts that the column is greater then the value
     *
     * @param string $column
     * @param string $value
     * @param string $conjunction
     * @return \PSX\Sql\Condition
     */
    public function greater($column, $value, $conjunction = 'AND')
    {
        return $this->addExpression(new Condition\Basic($column, '>', $value, $conjunction));
    }

    /**
     * Asserts that the column is greater or equal to the value
     *
     * @param string $column
     * @param string $value
     * @param string $conjunction
     * @return \PSX\Sql\Condition
     */
    public function greaterThen($column, $value, $conjunction = 'AND')
    {
        return $this->addExpression(new Condition\Basic($column, '>=', $value, $conjunction));
    }

    /**
     * Asserts that the column is lower then the value
     *
     * @param string $column
     * @param string $value
     * @param string $conjunction
     * @return \PSX\Sql\Condition
     */
    public function lower($column, $value, $conjunction = 'AND')
    {
        return $this->addExpression(new Condition\Basic($column, '<', $value, $conjunction));
    }

    /**
     * Asserts that the column is lower or equal to the value
     *
     * @param string $column
     * @param string $value
     * @param string $conjunction
     * @return \PSX\Sql\Condition
     */
    public function lowerThen($column, $value, $conjunction = 'AND')
    {
        return $this->addExpression(new Condition\Basic($column, '<=', $value, $conjunction));
    }

    /**
     * Asserts that the column is like the value
     *
     * @param string $column
     * @param string $value
     * @param string $conjunction
     * @return \PSX\Sql\Condition
     */
    public function like($column, $value, $conjunction = 'AND')
    {
        return $this->addExpression(new Condition\Basic($column, 'LIKE', $value, $conjunction));
    }

    /**
     * Asserts that the column is not like the value
     *
     * @param string $column
     * @param string $value
     * @param string $conjunction
     * @return \PSX\Sql\Condition
     */
    public function notLike($column, $value, $conjunction = 'AND')
    {
        return $this->addExpression(new Condition\Basic($column, 'NOT LIKE', $value, $conjunction));
    }

    /**
     * Asserts that the column is between the left and right value
     *
     * @param string $column
     * @param string $left
     * @param string $right
     * @param string $conjunction
     * @return \PSX\Sql\Condition
     */
    public function between($column, $left, $right, $conjunction = 'AND')
    {
        return $this->addExpression(new Condition\Between($column, $left, $right, $conjunction));
    }

    /**
     * Asserts that the column is in the array of values
     *
     * @param string $column
     * @param array $values
     * @param string $conjunction
     * @return \PSX\Sql\Condition
     */
    public function in($column, array $values, $conjunction = 'AND')
    {
        return $this->addExpression(new Condition\In($column, $values, $conjunction));
    }

    /**
     * Asserts that the column is null
     *
     * @param string $column
     * @param string $conjunction
     * @return \PSX\Sql\Condition
     */
    public function nil($column, $conjunction = 'AND')
    {
        return $this->addExpression(new Condition\Nil($column, $conjunction));
    }

    /**
     * Asserts that the column is not null
     *
     * @param string $column
     * @param string $conjunction
     * @return \PSX\Sql\Condition
     */
    public function notNil($column, $conjunction = 'AND')
    {
        return $this->addExpression(new Condition\NotNil($column, $conjunction));
    }

    /**
     * Adds an raw SQL expression
     *
     * @param string $statment
     * @param array $values
     * @param string $conjunction
     * @return \PSX\Sql\Condition
     */
    public function raw($statment, array $values = array(), $conjunction = 'AND')
    {
        return $this->addExpression(new Condition\Raw($statment, $values, $conjunction));
    }

    /**
     * Asserts that the column matches the provided regular expression
     *
     * @param string $column
     * @param string $regexp
     * @param string $conjunction
     * @return \PSX\Sql\Condition
     */
    public function regexp($column, $regexp, $conjunction = 'AND')
    {
        return $this->addExpression(new Condition\Regexp($column, $regexp, $conjunction));
    }

    /**
     * Adds an expression
     *
     * @param \PSX\Sql\Condition\ExpressionInterface $expr
     * @return \PSX\Sql\Condition
     */
    public function addExpression(ExpressionInterface $expr)
    {
        $this->expressions[] = $expr;

        return $this;
    }

    /**
     * Sets whether the expression is inverse
     *
     * @param boolean $isInverse
     * @return \PSX\Sql\Condition
     */
    public function setInverse($isInverse)
    {
        $this->isInverse = $isInverse;

        return $this;
    }

    /**
     * Returns the count of conditions
     *
     * @return integer
     */
    public function count()
    {
        return count($this->expressions);
    }

    /**
     * Merges an existing condition
     *
     * @param \PSX\Sql\Condition $condition
     * @return \PSX\Sql\Condition
     */
    public function merge(Condition $condition)
    {
        $this->expressions = array_merge($this->expressions, $condition->toArray());

        return $this;
    }

    /**
     * Returns an expression by the column name or null
     *
     * @param string $column
     * @return \PSX\Sql\Condition\ExpressionInterface
     */
    public function get($column)
    {
        foreach ($this->expressions as $expr) {
            if ($expr->getColumn() == $column) {
                return $expr;
            }
        }

        return null;
    }

    /**
     * Removes an condition containing an specific column
     *
     * @param string $column
     */
    public function remove($column)
    {
        foreach ($this->expressions as $key => $expr) {
            if ($expr->getColumn() == $column) {
                unset($this->expressions[$key]);
            }
        }
    }

    /**
     * Removes all columns
     *
     * @return void
     */
    public function removeAll()
    {
        $this->expressions = array();
    }

    /**
     * Returns all conditions as array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->expressions;
    }

    /**
     * Returns whether an condition exist
     *
     * @return boolean
     */
    public function hasCondition()
    {
        return count($this->expressions) > 0;
    }

    /**
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @return string
     */
    public function getStatment(AbstractPlatform $platform = null)
    {
        if ($platform === null) {
            $platform = new MySqlPlatform();
        }

        return 'WHERE ' . $this->getExpression($platform);
    }

    /**
     * Returns the SQL as string containing prepared statment placeholder
     *
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @return string
     */
    public function getExpression(AbstractPlatform $platform)
    {
        $len = count($this->expressions);
        $con = '';
        $i   = 0;

        if (empty($this->expressions)) {
            return $this->isInverse ? '1 = 0' : '1 = 1';
        }

        foreach ($this->expressions as $key => $expr) {
            $con.= $expr->getExpression($platform);
            $con.= ($i == $len - 1) ? '' : ' ' . $expr->getConjunction() . ' ';

            $i++;
        }

        return ($this->isInverse ? '!' : '') . '(' . $con . ')';
    }

    /**
     * Returns the parameters as array
     *
     * @return array
     */
    public function getValues()
    {
        $params = array();
        foreach ($this->expressions as $expr) {
            $values = $expr->getValues();
            foreach ($values as $value) {
                if ($value instanceof \DateTime) {
                    $params[] = $value->format('Y-m-d H:i:s');
                } else {
                    $params[] = $value;
                }
            }
        }

        return $params;
    }

    public function getArray()
    {
        $result = array();
        foreach ($this->expressions as $expr) {
            if ($expr instanceof Condition\In) {
                $result[$expr->getColumn()] = $expr->getValues();
            } elseif ($expr instanceof Condition\Nil) {
                $result[$expr->getColumn()] = null;
            } elseif ($expr instanceof Condition\Basic) {
                $result[$expr->getColumn()] = current($expr->getValues());
            }
        }

        return $result;
    }

    public static function fromCriteria(array $criteria)
    {
        $condition = new self();

        foreach ($criteria as $field => $value) {
            if (is_array($value)) {
                $condition->addExpression(new Condition\In($field, $value));
            } elseif (is_null($value)) {
                $condition->addExpression(new Condition\Nil($field));
            } elseif (is_scalar($value)) {
                $condition->addExpression(new Condition\Basic($field, '=', $value));
            }
        }

        return $condition;
    }
}
