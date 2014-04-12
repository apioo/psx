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

namespace PSX\Controller;

use PSX\Base;
use PSX\DateTime;
use PSX\Data\NotFoundException;
use PSX\Data\RecordInterface;
use PSX\Data\Writer;
use PSX\Data\WriterFactory;
use PSX\Data\WriterInterface;
use PSX\ControllerAbstract;
use PSX\Sql;
use PSX\Sql\Condition;

/**
 * ApiAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ApiAbstract extends ControllerAbstract
{
	protected $_requestParams;

	/**
	 * Checks whether the preferred writer is an instance of the writer class
	 *
	 * @param string $writerClass
	 * @return boolean
	 */
	protected function isWriter($writerClass)
	{
		return $this->getPreferredWriter() instanceof $writerClass;
	}

	/**
	 * Returns an condition object depending on the filter params
	 *
	 * @param string $dateColumn
	 * @return PSX\Sql\Condition
	 */
	protected function getRequestCondition($dateColumn = 'date')
	{
		$con          = new Condition();
		$params       = $this->getRequestParams();
		$filterBy     = $params['filterBy'];
		$filterOp     = $params['filterOp'];
		$filterValue  = $params['filterValue'];
		$updatedSince = $params['updatedSince'];

		if(!empty($filterBy) && !empty($filterOp) && !empty($filterValue))
		{
			switch($filterOp)
			{
				case 'contains':
					$con->add($filterBy, 'LIKE', '%' . $filterValue . '%');
					break;

				case 'equals':
					$con->add($filterBy, '=', $filterValue);
					break;

				case 'startsWith':
					$con->add($filterBy, 'LIKE', $filterValue . '%');
					break;

				case 'present':
					$con->add($filterBy, 'IS NOT', 'NULL', 'AND');
					$con->add($filterBy, 'NOT LIKE', '');
					break;
			}
		}

		if($updatedSince instanceof \DateTime)
		{
			$con->add($dateColumn, '>', $updatedSince->format(DateTime::SQL));
		}

		return $con;
	}

	/**
	 * Returns an associative array containing all available request parameters
	 *
	 * @return array
	 */
	protected function getRequestParams()
	{
		if($this->_requestParams === null)
		{
			$fields       = $this->request->getUrl()->getParam('fields');
			$updatedSince = $this->request->getUrl()->getParam('updatedSince');
			$count        = $this->request->getUrl()->getParam('count');
			$filterBy     = $this->request->getUrl()->getParam('filterBy');
			$filterOp     = $this->request->getUrl()->getParam('filterOp');
			$filterValue  = $this->request->getUrl()->getParam('filterValue');
			$sortBy       = $this->request->getUrl()->getParam('sortBy');
			$sortOrder    = $this->request->getUrl()->getParam('sortOrder');
			$startIndex   = $this->request->getUrl()->getParam('startIndex');

			if(!empty($fields))
			{
				$parts  = explode(',', $fields);
				$fields = array();

				foreach($parts as $field)
				{
					$field = trim($field);

					if(strlen($field) > 1 && strlen($field) < 32 && ctype_alnum($field))
					{
						$fields[] = $field;
					}
				}
			}
			else
			{
				$fields = array();
			}

			$updatedSince = !empty($updatedSince) ? new \DateTime($updatedSince) : null;
			$count        = !empty($count) ? intval($count) : null;
			$filterBy     = !empty($filterBy) && ctype_alnum($filterBy) && strlen($filterBy) < 32 ? $filterBy : null;
			$filterOp     = !empty($filterOp) && in_array($filterOp, array('contains', 'equals', 'startsWith', 'present')) ? $filterOp : null;
			$filterValue  = !empty($filterValue) && strlen($filterValue) < 128 ? $filterValue : null;
			$sortBy       = !empty($sortBy) && strlen($sortBy) < 128 ? $sortBy : null;
			$startIndex   = !empty($startIndex) ? intval($startIndex) : null;

			if(!empty($sortOrder))
			{
				switch(strtolower($sortOrder))
				{
					case 'asc':
					case 'ascending':
						$sortOrder = Sql::SORT_ASC;
						break;

					case 'desc':
					case 'descending':
						$sortOrder = Sql::SORT_DESC;
						break;

					default:
						$sortOrder = null;
						break;
				}
			}
			else
			{
				$sortOrder = null;
			}

			$this->_requestParams = array(
				'fields'       => $fields,
				'updatedSince' => $updatedSince,
				'count'        => $count,
				'filterBy'     => $filterBy,
				'filterOp'     => $filterOp,
				'filterValue'  => $filterValue,
				'sortBy'       => $sortBy,
				'sortOrder'    => $sortOrder,
				'startIndex'   => $startIndex,
			);
		}

		return $this->_requestParams;
	}
}
