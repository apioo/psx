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

namespace PSX\Handler\Doctrine;

use Closure;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PSX\Data\RecordInterface;
use PSX\Handler\HandlerAbstract;

/**
 * RepositoryHandler
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RepositoryHandler extends HandlerAbstract
{
	protected $em;
	protected $repository;

	public function __construct(EntityManager $em, EntityRepository $repository)
	{
		$this->em         = $em;
		$this->repository = $repository;
	}

	public function getAll(array $fields, $startIndex = 0, $count = 16, $sortBy = null, $sortOrder = null, Condition $con = null)
	{
		$criteria = $con !== null ? $con->getArray() : array();

		return $this->repository->findBy($criteria, array($sortBy => $sortOrder), $count, $startIndex);
	}

	public function getBy(Condition $con, array $fields = array())
	{
		return $this->repository->findBy($con->getArray());
	}

	public function getOneBy(Condition $con, array $fields = array())
	{
		return $this->repository->findOneBy($con->getArray());
	}

	public function get($id, array $fields = array())
	{
		return $this->repository->find($id);
	}

	public function getSupportedFields()
	{
		return $this->getClassMetadata()->getFieldNames();
	}

	public function getCount(Condition $con = null)
	{
		$qb = $this->createQueryBuilder()
			->select('COUNT(' . $this->getClassMetadata()->getSingleIdentifierFieldName() . ')');

		if($con !== null)
		{
			$values = $con->getArray();

			foreach($values as $name => $value)
			{
				$qb->andWhere($name . ' = :' . $name);
				$qb->setParameter($name, $value);
			}
		}

		return (int) $qb->getQuery()->getSingleScalarResult();
	}

	public function create(RecordInterface $record)
	{
		$entity = $this->createEntity($record);

		$this->em->persist($entity);
		$this->em->flush();
	}

	public function update(RecordInterface $record)
	{
		$method = 'get' . ucfirst($this->getPrimaryIdField());
		$entity = $this->manager->getRepository($this->entityName)->find($record->$method());
		$entity = $this->createEntity($record, $entity);

		$this->em->persist($entity);
		$this->em->flush();
	}

	public function delete(RecordInterface $record)
	{
		$method = 'get' . ucfirst($this->getPrimaryIdField());
		$entity = $this->manager->getRepository($this->entityName)->find($record->$method());
		$entity = $this->createEntity($record, $entity);

		$this->em->remove($entity);
		$this->em->flush();
	}

	protected function getClassMetadata()
	{
		return $this->em->getClassMetadata($this->repository->getClassName());
	}
}
