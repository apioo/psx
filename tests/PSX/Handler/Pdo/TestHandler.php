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

namespace PSX\Handler\Pdo;

use PSX\Handler\PdoHandlerAbstract;
use PSX\Handler\MappingAbstract;
use PSX\Sql;
use PSX\Sql\Condition;

/**
 * TestHandler
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TestHandler extends PdoHandlerAbstract
{
	public function getMapping()
	{
		return new Mapping(array(
			'id'     => MappingAbstract::TYPE_INTEGER | 10 | MappingAbstract::ID_PROPERTY,
			'userId' => MappingAbstract::TYPE_INTEGER | 10,
			'title'  => MappingAbstract::TYPE_STRING | 32,
			'date'   => MappingAbstract::TYPE_DATETIME,
		));
	}

	protected function getSelectStatment(array $fields, $startIndex = 0, $count = 16, $sortBy = null, $sortOrder = null, Condition $con = null)
	{
		if(empty($fields))
		{
			throw new InvalidArgumentException('Field must not be empty');
		}

		if($con !== null)
		{
			$sql    = 'SELECT ' . implode(', ', array_map('PSX\Sql::helpQuote', $fields)) . ' FROM `psx_handler_comment` ' . $con->getStatment() . ' ';
			$params = $con->getValues();
		}
		else
		{
			$sql    = 'SELECT ' . implode(', ', array_map('PSX\Sql::helpQuote', $fields)) . ' FROM `psx_handler_comment` ';
			$params = array();
		}

		if($sortBy !== null)
		{
			$sql.= 'ORDER BY `' . $sortBy . '` ' . ($sortOrder == Sql::SORT_ASC ? 'ASC' : 'DESC') . ' ';
		}

		if($startIndex !== null)
		{
			$sql.= 'LIMIT ' . intval($startIndex) . ', ' . intval($count);
		}

		$statment = $this->pdo->prepare($sql);

		foreach($params as $i => $value)
		{
			$statment->bindValue($i + 1, $value, Sql::getType($value));
		}

		return $statment;
	}

	protected function getCountStatment(Condition $con = null)
	{
		if($con !== null)
		{
			$sql    = 'SELECT COUNT(`id`) FROM `psx_handler_comment` ' . $con->getStatment() . ' ';
			$params = $con->getValues();
		}
		else
		{
			$sql    = 'SELECT COUNT(`id`) FROM `psx_handler_comment` ';
			$params = array();
		}

		$statment = $this->pdo->prepare($sql);

		foreach($params as $i => $value)
		{
			$statment->bindParam($i + 1, $value, Sql::getType($value));
		}

		return $statment;
	}
}
