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

namespace PSX\Handler;

use InvalidArgumentException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\From;
use Doctrine\ORM\Query\Expr\Join;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Sql;
use PSX\Sql\Condition;

/**
 * Handler which can operate on an entity repository. You can extend this 
 * handler and implement the method getDefaultSelect() wich simply returns an 
 * query builder where the from and join fields are set. All other field  
 * selection is made by the handler. This handler uses partial selection in 
 * order to select only specific fields
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class DoctrineHandlerAbstract extends HandlerAbstract
{
	protected $manager;
	protected $entityName;

	protected $_select;
	protected $_partialFields;
	protected $_idFields;

	public function __construct(EntityManager $manager)
	{
		$this->manager    = $manager;
		$this->entityName = $this->getEntityName();
	}

	public function getAll(array $fields = null, $startIndex = null, $count = null, $sortBy = null, $sortOrder = null, Condition $condition = null)
	{
		$startIndex = $startIndex !== null ? (int) $startIndex : 0;
		$count      = !empty($count)       ? (int) $count      : 16;
		$sortBy     = $sortBy     !== null ? $sortBy           : $this->getPrimaryIdField();
		$sortOrder  = $sortOrder  !== null ? (int) $sortOrder  : Sql::SORT_DESC;

		$fields = $this->getValidFields($fields);

		if(!in_array($sortBy, $this->getSupportedFields()))
		{
			$sortBy = $this->getPrimaryIdField();
		}

		$qb = $this
			->getSelect()
			->select($this->getQuerySelect($fields))
			->orderBy($this->getQueryOrderBy($sortBy), $sortOrder == Sql::SORT_ASC ? 'ASC' : 'DESC')
			->setFirstResult($startIndex)
			->setMaxResults($count);

		if($condition !== null && $condition->hasCondition())
		{
			$values      = $condition->toArray();
			$conjunction = null;

			foreach($values as $key => $row)
			{
				if($conjunction != null)
				{
					if($conjunction == 'OR' || $conjunction == '||')
					{
						$qb->orWhere($this->getColumnNameByAlias($row[Condition::COLUMN]) . ' ' . $row[Condition::OPERATOR] . ' ?' . $key);
						$qb->setParameter($key, $row[Condition::VALUE]);
					}
					else
					{
						$qb->andWhere($this->getColumnNameByAlias($row[Condition::COLUMN]) . ' ' . $row[Condition::OPERATOR] . ' ?' . $key);
						$qb->setParameter($key, $row[Condition::VALUE]);
					}
				}
				else
				{
					$qb->where($this->getColumnNameByAlias($row[Condition::COLUMN]) . ' ' . $row[Condition::OPERATOR] . ' ?' . $key);
					$qb->setParameter($key, $row[Condition::VALUE]);
				}

				$conjunction = $row[Condition::CONJUNCTION];
			}
		}

		return $qb->getQuery()->getResult(Doctrine\RecordHydrator::HYDRATE_RECORD);
	}

	public function get($id)
	{
		$condition = new Condition(array($this->getPrimaryIdField(), '=', $id));

		return $this->getOneBy($condition);
	}

	public function getSupportedFields()
	{
		$result = array();
		$fields = $this->getPartialFields();
		$i      = 0;

		foreach($fields as $key => $field)
		{
			if($i > 0)
			{
				$func = function($k) use ($key){
					return $key . ucfirst($k);
				};

				$field = array_map($func, $field);
			}

			$result = array_merge($result, $field);

			$i++;
		}

		return array_diff($result, $this->getRestrictedFields());
	}

	public function getCount(Condition $condition = null)
	{
		$qb = $this
			->getSelect()
			->select('count(' . $this->getColumnNameByAlias($this->getPrimaryIdField()) . ')');

		if($condition !== null && $condition->hasCondition())
		{
			$values = $condition->toArray();

			foreach($values as $key => $row)
			{
				$qb->andWhere($this->getColumnNameByAlias($row[Condition::COLUMN]) . ' = ?' . $key);
				$qb->setParameter($key, $row[Condition::VALUE]);
			}
		}

		return $qb->getQuery()->getSingleScalarResult();
	}

	public function create(RecordInterface $record)
	{
		$entity = $this->createEntity($record);

		$this->manager->persist($entity);
		$this->manager->flush();
	}

	public function update(RecordInterface $record)
	{
		$method = 'get' . ucfirst($this->getPrimaryIdField());
		$entity = $this->manager->getRepository($this->entityName)->find($record->$method());
		$entity = $this->createEntity($record, $entity);

		$this->manager->persist($entity);
		$this->manager->flush();
	}

	public function delete(RecordInterface $record)
	{
		$method = 'get' . ucfirst($this->getPrimaryIdField());
		$entity = $this->manager->getRepository($this->entityName)->find($record->$method());
		$entity = $this->createEntity($record, $entity);

		$this->manager->remove($entity);
		$this->manager->flush();
	}

	/**
	 * Returns the default query builder. In most cases you only have to set
	 * the from and join fields all other settings are made by the handler i.e.
	 * <code>
	 * return $this->manager->createQueryBuilder()
	 *  ->from('Foo\Entity', 'foo')
	 *  ->innerJoin('foo.bar', 'bar')
	 * </code>
	 *
	 * @return Doctrine\ORM\QueryBuilder
	 */
	abstract protected function getDefaultSelect();

	/**
	 * Returns the entity on wich the handler operates
	 *
	 * @return string
	 */
	protected function getEntityName()
	{
		return current($this->getSelect()->getRootEntities());
	}

	protected function getPrettyEntityName()
	{
		$parts = explode('\\', $this->entityName);

		return lcfirst(end($parts));
	}

	/**
	 * Returns the partial fields as array based on the default select
	 *
	 * @return array
	 */
	protected function getPartialFields()
	{
		if($this->_partialFields === null)
		{
			$this->_idFields = array();

			$map    = array();
			$select = $this->getSelect();

			// from fields
			$from = $select->getDQLPart('from');
			foreach($from as &$fromClause)
			{
				if(is_string($fromClause))
				{
					$spacePos = strrpos($fromClause, ' ');
					$from     = substr($fromClause, 0, $spacePos);
					$alias    = substr($fromClause, $spacePos + 1);

					$fromClause = new From($from, $alias);
				}

				$map[$fromClause->getAlias()] = $fromClause->getFrom();
			}

			// join fields
			$joins = $select->getDQLPart('join');
			if(is_array($joins))
			{
				foreach($joins as $joinList)
				{
					foreach($joinList as $join)
					{
						if($join instanceof Join)
						{
							list($alias, $property) = explode('.', $join->getJoin());

							if(isset($map[$alias]))
							{
								$map[$join->getAlias()] = $this->manager->getClassMetadata($map[$alias])->getAssociationTargetClass($property);
							}
						}
					}
				}
			}

			foreach($map as $key => $className)
			{
				$classMeta    = $this->manager->getClassMetadata($className);
				$fields       = $classMeta->getFieldNames();
				$associations = $classMeta->getAssociationNames();

				foreach($associations as $associationName)
				{
					$association = $classMeta->getAssociationMapping($associationName);

					if($association['type'] & ClassMetadataInfo::TO_ONE)
					{
						$fields[] = $associationName;
					}
				}

				$this->_partialFields[$key] = $fields;
				$this->_idFields[$key]      = $classMeta->getSingleIdentifierFieldName();
			}
		}

		return $this->_partialFields;
	}

	protected function getSelect()
	{
		if($this->_select === null)
		{
			$this->_select = $this->getDefaultSelect();
		}

		$select = clone $this->_select;

		return $select;
	}

	protected function getPrimaryIdField()
	{
		return $this->manager->getClassMetadata($this->entityName)->getSingleIdentifierFieldName();
	}

	protected function getQuerySelect(array $selectedFields)
	{
		$dql    = array();
		$fields = $this->getPartialFields();
		$i      = 0;

		foreach($fields as $key => $field)
		{
			$values = array();

			if($i > 0)
			{
				$func = function($k) use ($key){
					return $key . ucfirst($k);
				};

				$values = array_map($func, $field);
				$result = array();

				foreach($values as $k => $v)
				{
					if(in_array($v, $selectedFields))
					{
						$result[] = $field[$k];
					}
				}

				$values = $result;
			}
			else
			{
				$values = array_intersect($field, $selectedFields);
			}

			// partial selection must include the id field
			if(isset($this->_idFields[$key]))
			{
				$values[] = $this->_idFields[$key];
			}

			if(!empty($values))
			{
				$dql[] = 'partial ' . $key . '.{' . implode(', ', $values) . '}';
			}

			$i++;
		}

		return implode(', ', $dql);
	}

	protected function getQueryOrderBy($sortBy)
	{
		return $this->getColumnNameByAlias($sortBy);
	}

	/**
	 * Returns the dql column name from an alias
	 *
	 * @return string
	 */
	protected function getColumnNameByAlias($column)
	{
		$fields = $this->getPartialFields();
		$i      = 0;

		foreach($fields as $key => $field)
		{
			if($i > 0)
			{
				$func = function($k) use ($key){
					return $key . ucfirst($k);
				};

				$values = array_map($func, $field);

				foreach($values as $k => $v)
				{
					if($v == $column)
					{
						return $key . '.' . $field[$k];
					}
				}
			}
			else
			{
				foreach($field as $v)
				{
					if($v == $column)
					{
						return $key . '.' . $v;
					}
				}
			}

			$i++;
		}

		throw new InvalidArgumentException('Invalid column');
	}

	protected function createEntity(RecordInterface $record, $entity = null)
	{
		if($entity === null)
		{
			$entity = new $this->entityName();
		}

		// we map simple value fields but make a check for references
		$classMeta    = $this->manager->getClassMetadata($this->entityName);
		$fields       = $classMeta->getFieldNames();
		$associations = $classMeta->getAssociationNames();
		$data         = $record->getRecordInfo()->getData();

		foreach($data as $key => $value)
		{
			$method = 'set' . ucfirst($key);

			if(in_array($key, $fields))
			{
				if(is_callable(array($entity, $method)))
				{
					$entity->$method($value);
				}
			}
			else if(in_array($key, $associations))
			{
				$association = $classMeta->getAssociationMapping($key);

				if($association['type'] & ClassMetadataInfo::TO_ONE)
				{
					if(is_numeric($value))
					{
						// if its in integer look for the connection
						$target    = $classMeta->getAssociationTargetClass($key);
						$reference = $this->manager->getRepository($target)->find($value);

						if(is_callable(array($entity, $method)))
						{
							$entity->$method($reference);
						}
					}
					else if(is_array($value))
					{
						// @TOOD if its an array we probably want to create the 
						// entity
					}
				}
				else if($association['type'] & ClassMetadataInfo::TO_MANY)
				{
					// @TODO probably we want make an option to create multiple
					// entities from one request
				}
			}
			else
			{
				// unknown field
			}
		}

		return $entity;
	}
}
