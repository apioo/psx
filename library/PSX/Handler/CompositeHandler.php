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

/**
 * CompositeHandler
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class CompositeHandler implements HandlerQueryInterface, HandlerManipulationInterface
{
	protected $query;
	protected $manipulation;

	public function __construct(HandlerQueryInterface $query, HandlerManipulationInterface $manipulation)
	{
		$this->query        = $query;
		$this->manipulation = $manipulation;
	}

	public function getAll(array $fields = null, $startIndex = null, $count = null, $sortBy = null, $sortOrder = null, Condition $condition = null)
	{
		return $this->query->getAll($fields, $startIndex, $count, $sortBy, $sortOrder, $condition);
	}

	public function getBy(Condition $condition, array $fields = null)
	{
		return $this->query->getBy($condition, $fields);
	}

	public function getOneBy(Condition $condition, array $fields = null)
	{
		return $this->query->getOneBy($condition, $fields);
	}

	public function get($id)
	{
		return $this->query->get($id);
	}

	public function getCount(Condition $condition = null)
	{
		return $this->query->getCount($condition);
	}

	public function getSupportedFields()
	{
		return $this->query->getSupportedFields();
	}

	public function create(RecordInterface $record)
	{
		return $this->manipulation->create($record);
	}

	public function update(RecordInterface $record)
	{
		return $this->manipulation->update($record);
	}

	public function delete(RecordInterface $record)
	{
		return $this->manipulation->delete($record);
	}
}
