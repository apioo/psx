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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use PSX\Data\Record;
use PSX\Data\Record\Mapper;
use PSX\Data\RecordInterface;
use PSX\Sql;
use PSX\Sql\Condition;

/**
 * Handler wich can operate on an entity repository
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class DoctrineHandlerAbstract extends HandlerAbstract
{
	protected $manager;
	protected $entityName;
	protected $repository;

	public function __construct(EntityManager $manager)
	{
		$this->manager    = $manager;
		$this->entityName = $this->getEntityName();
		$this->repository = $manager->getRepository($this->entityName);
	}

	public function getAll(array $fields = array(), $startIndex = 0, $count = 16, $sortBy = null, $sortOrder = null, Condition $con = null)
	{
		$startIndex = $startIndex !== null ? (integer) $startIndex : 0;
		$count      = !empty($count)       ? (integer) $count      : 16;
		$sortBy     = $sortBy     !== null ? $sortBy               : $this->getPrimaryIdField();
		$sortOrder  = $sortOrder  !== null ? (integer) $sortOrder  : Sql::SORT_DESC;

		$fields = array_intersect($fields, $this->getSupportedFields());

		if(empty($fields))
		{
			$fields = $this->getSupportedFields();
		}

		$qb = $this
			->getDefaultSelect($fields, $sortBy, $sortOrder)
			->setFirstResult($startIndex)
			->setMaxResults($count);

		if($con !== null && $con->hasCondition())
		{
			$values      = $con->toArray();
			$conjunction = null;

			foreach($values as $key => $row)
			{
				if($conjunction != null)
				{
					if($conjunction == 'OR' || $conjunction == '||')
					{
						$qb->orWhere('r.' . $row[Condition::COLUMN] . ' ' . $row[Condition::OPERATOR] . ' ?' . $key);
						$qb->setParameter($key, $row[Condition::VALUE]);
					}
					else
					{
						$qb->andWhere('r.' . $row[Condition::COLUMN] . ' ' . $row[Condition::OPERATOR] . ' ?' . $key);
						$qb->setParameter($key, $row[Condition::VALUE]);
					}
				}
				else
				{
					$qb->where('r.' . $row[Condition::COLUMN] . ' ' . $row[Condition::OPERATOR] . ' ?' . $key);
					$qb->setParameter($key, $row[Condition::VALUE]);
				}

				$conjunction = $row[Condition::CONJUNCTION];
			}
		}

		return $qb->getQuery()->getResult(Doctrine\RecordHydrator::HYDRATE_RECORD);
	}

	public function get($id, array $fields = array())
	{
		$con = new Condition(array($this->getPrimaryIdField(), '=', $id));

		return $this->getOneBy($con, $fields);
	}

	public function getSupportedFields()
	{
		return $this->manager->getClassMetadata($this->entityName)->getFieldNames();
	}

	public function getCount(Condition $con = null)
	{
		$qb = $this->manager->createQueryBuilder();
		$qb->select('count(r.' . $this->getPrimaryIdField() . ')');
		$qb->from($this->entityName, 'r');

		if($con !== null && $con->hasCondition())
		{
			$values = $con->toArray();

			foreach($values as $key => $row)
			{
				$qb->andWhere('r.' . $row[Condition::COLUMN] . ' = ?' . $key);
				$qb->setParameter($key, $row[Condition::VALUE]);
			}
		}

		return $qb->getQuery()->getSingleScalarResult();
	}

	public function getRecord($id = null)
	{
		if(empty($id))
		{
			$keys    = $this->getSupportedFields();
			$values  = array_fill(0, count($keys), null);

			return new Record($this->getPrettyEntityName(), array_combine($keys, $values));
		}
		else
		{
			return $this->get($id, $this->getSupportedFields());
		}
	}

	public function create(RecordInterface $record)
	{
		$entity = new $this->entityName();

		$mapper = new Mapper();
		$mapper->map($record, $entity);

		$this->manager->persist($entity);
		$this->manager->flush();
	}

	public function update(RecordInterface $record)
	{
		$entity = new $this->entityName();

		$mapper = new Mapper();
		$mapper->map($record, $entity);

		$this->manager->persist($record);
		$this->manager->flush();
	}

	public function delete(RecordInterface $record)
	{
		$entity = new $this->entityName();

		$mapper = new Mapper();
		$mapper->map($record, $entity);

		$this->manager->remove($record);
		$this->manager->flush();
	}

	/**
	 * Returns the entity on wich the handler operates
	 *
	 * @return string
	 */
	abstract public function getEntityName();

	protected function getDefaultSelect(array $fields, $sortBy, $sortOrder)
	{
		$select = array();
		foreach($fields as $field)
		{
			$select[] = 'r.' . $field;
		}

		$qb = $this->manager->createQueryBuilder();
		$qb->select($select)
			->from($this->entityName, 'r')
			->orderBy('r.' . $sortBy, $sortOrder == Sql::SORT_ASC ? 'ASC' : 'DESC');

		return $qb;
	}

	protected function getPrettyEntityName()
	{
		$parts = explode('\\', $this->entityName);

		return lcfirst(end($parts));
	}

	protected function getPrimaryIdField()
	{
		return $this->manager->getClassMetadata($this->entityName)->getSingleIdentifierFieldName();
	}
}
