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

namespace PSX\Handler;

use DOMDocument;
use DOMElement;
use InvalidArgumentException;
use PSX\Data\Record;
use PSX\Sql;
use PSX\Sql\Condition;

/**
 * Handler wich can query an underlying DOMDocument
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class DomHandlerAbstract extends DataHandlerQueryAbstract
{
	protected $mapping;

	public function __construct()
	{
		$this->mapping = $this->getMapping();
	}

	public function getAll(array $fields = array(), $startIndex = 0, $count = 16, $sortBy = null, $sortOrder = null, Condition $con = null)
	{
		$startIndex = $startIndex !== null ? (integer) $startIndex : 0;
		$count      = !empty($count)       ? (integer) $count      : 16;
		$sortBy     = $sortBy     !== null ? $sortBy               : $this->mapping->getIdProperty();
		$sortOrder  = $sortOrder  !== null ? (integer) $sortOrder  : Sql::SORT_DESC;

		$fields = array_intersect($fields, $this->getSupportedFields());

		if(empty($fields))
		{
			$fields = $this->getSupportedFields();
		}

		$root = $this->mapping->getDom()->getElementsByTagName($this->mapping->getRoot())->item(0);

		if($root instanceof DOMElement)
		{
			$entries = $root->getElementsByTagName($this->mapping->getRecord());
			$sort    = array();
			$return  = array();

			for($i = 0; $i < $entries->length; $i++)
			{
				$entry     = $entries->item($i);
				$row       = array();
				$sortValue = null;

				for($j = 0; $j < $entry->childNodes->length; $j++)
				{
					if($entry->childNodes->item($j) instanceof DOMElement)
					{
						foreach($this->mapping->getFields() as $field => $type)
						{
							if($entry->childNodes->item($j)->nodeName == $field)
							{
								$value = $entry->childNodes->item($j)->nodeValue;

								$row[$field] = $this->unserializeType($value, $type);

								if($sortBy == $field)
								{
									$sortValue = $row[$field];
								}
							}
						}
					}
				}

				if($con !== null && $con->hasCondition())
				{
					if(!$this->isConditionFulfilled($con, $row))
					{
						continue;
					}
				}

				$return[] = $row;
				$sort[]   = $sortValue;
			}

			// sort
			if($sortOrder == Sql::SORT_ASC)
			{
				asort($sort);
			}
			else
			{
				arsort($sort);
			}

			$result = array();
			foreach($sort as $key => $value)
			{
				$row = array_intersect_key($return[$key], array_flip($fields));

				$result[] = new Record($this->mapping->getRecord(), $row);
			}

			return array_slice($result, $startIndex, $count);
		}
		else
		{
			throw new InvalidArgumentException('Could not find root element ' . $this->mapping->getRoot());
		}
	}

	public function get($id, array $fields = array())
	{
		$con = new Condition(array($this->mapping->getIdProperty(), '=', $id));

		return $this->getOneBy($con, $fields);
	}

	public function getSupportedFields()
	{
		return array_diff(array_keys($this->mapping->getFields()), $this->getRestrictedFields());
	}

	public function getCount(Condition $con = null)
	{
		$count = 0;
		$root  = $this->mapping->getDom()->getElementsByTagName($this->mapping->getRoot())->item(0);

		if($root instanceof DOMElement)
		{
			$entries = $root->getElementsByTagName($this->mapping->getRecord());

			for($i = 0; $i < $entries->length; $i++)
			{
				$entry = $entries->item($i);
				$row   = array();

				for($j = 0; $j < $entry->childNodes->length; $j++)
				{
					if($entry->childNodes->item($j) instanceof DOMElement)
					{
						foreach($this->mapping->getFields() as $field => $type)
						{
							if($entry->childNodes->item($j)->nodeName == $field)
							{
								$value = $entry->childNodes->item($j)->nodeValue;

								$row[$field] = $this->unserializeType($value, $type);
							}
						}
					}
				}

				if($con !== null && $con->hasCondition())
				{
					if(!$this->isConditionFulfilled($con, $row))
					{
						continue;
					}
				}

				$count++;
			}
		}

		return $count;
	}

	public function getRecord($id = null)
	{
		if(empty($id))
		{
			$fields  = $this->mapping->getFields();
			$keys    = array_keys($fields);
			$values  = array_fill(0, count($fields), null);

			return new Record($this->mapping->getRecord(), array_combine($keys, $values));
		}
		else
		{
			$fields  = array_keys($this->mapping->getFields());

			return $this->get($id, $fields);
		}
	}

	/**
	 * Returns the mapping informations for this document
	 *
	 * @return PSX\Handler\Dom\Mapping
	 */
	abstract public function getMapping();
}
