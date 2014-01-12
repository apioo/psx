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

namespace PSX\Module;

use DateTime;
use PSX\Atom;
use PSX\Atom\Entry;
use PSX\Atom\Text;
use PSX\Data\Message;
use PSX\Data\Record\Mapper;
use PSX\Data\Record\Mapper\Rule;
use PSX\Data\RecordInterface;
use PSX\Data\WriterInterface;
use PSX\Module\ApiAbstract;
use PSX\Util\Uuid;
use PSX\Filter\FilterDefinition;
use PSX\Module\HandlerManipulationInterface;
use PSX\Module\HandlerQueryInterface;

/**
 * This module simplifies creating an API from an handler
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link	http://phpsx.org
 */
class HandlerApiAbstract extends ApiAbstract
{
	/**
	 * @httpMethod GET
	 * @path /
	 */
	public function doIndex()
	{
		try
		{
			if(!$this->getHandler() instanceof HandlerQueryInterface)
			{
				throw new Exception('Method not allowed', 405);
			}

			$params = $this->getRequestParams();
			$result = $this->getHandler()->getCollection($params['fields'], 
					$params['startIndex'], 
					$params['count'], 
					$params['sortBy'], 
					$params['sortOrder'], 
					$this->getRequestCondition());

			if($this->isWriter(WriterInterface::ATOM))
			{
				$this->setResponse($this->getAtomRecord($result));
			}
			else
			{
				$this->setResponse($result);
			}
		}
		catch(\Exception $e)
		{
			if($this->isWriter(WriterInterface::ATOM))
			{
				$msg = new Entry();
				$msg->setId(Uuid::nameBased($e->getMessage()));
				$msg->setTitle($e->getMessage());
				$msg->setUpdated(new DateTime());
			}
			else
			{
				$msg = new Message($e->getMessage(), false);
			}

			$this->setResponse($msg, null, $e->getCode());
		}
	}

	/**
	 * @httpMethod POST
	 * @path /
	 */
	public function doInsert()
	{
		try
		{
			if(!$this->getHandler() instanceof HandlerManipulationInterface)
			{
				throw new Exception('Method not allowed', 405);
			}

			$record = $this->getHandler()->getRecord();
			$record = $this->import($record);

			$this->beforeValidate($record);

			// validate
			$filterDefinition = $this->getFilterDefinition();

			if($filterDefinition instanceof FilterDefinition)
			{
				$filterDefinition->validate($record);
			}

			$this->afterValidate($record);

			// insert
			$this->getHandler()->create($record);

			// message
			$msg = new Message('You have successful create a ' . $record->getName(), true);

			$this->setResponse($msg);
		}
		catch(\Exception $e)
		{
			$msg = new Message($e->getMessage(), false);

			$this->setResponse($msg, null, $e->getCode());
		}
	}

	/**
	 * @httpMethod PUT
	 * @path /
	 */
	public function doUpdate()
	{
		try
		{
			if(!$this->getHandler() instanceof HandlerManipulationInterface)
			{
				throw new Exception('Method not allowed', 405);
			}

			$record = $this->getHandler()->getRecord();
			$record = $this->import($record);

			$this->beforeValidate($record);

			// validate
			$filterDefinition = $this->getFilterDefinition();

			if($filterDefinition instanceof FilterDefinition)
			{
				$filterDefinition->validate($record);
			}

			$this->afterValidate($record);

			// update
			$this->getHandler()->update($record);

			// message
			$msg = new Message('You have successful update a ' . $record->getName(), true);

			$this->setResponse($msg);
		}
		catch(\Exception $e)
		{
			$msg = new Message($e->getMessage(), false);

			$this->setResponse($msg, null, $e->getCode());
		}
	}

	/**
	 * @httpMethod DELETE
	 * @path /
	 */
	public function doDelete()
	{
		try
		{
			if(!$this->getHandler() instanceof HandlerManipulationInterface)
			{
				throw new Exception('Method not allowed', 405);
			}

			$record = $this->getHandler()->getRecord();
			$record = $this->import($record);

			$this->beforeValidate($record);

			// validate
			$filterDefinition = $this->getFilterDefinition();

			if($filterDefinition instanceof FilterDefinition)
			{
				$filterDefinition->validate($record);
			}

			$this->afterValidate($record);

			// delete
			$this->getHandler()->delete($record);

			// message
			$msg = new Message('You have successful delete a ' . $record->getName(), true);

			$this->setResponse($msg);
		}
		catch(\Exception $e)
		{
			$msg = new Message($e->getMessage(), false);

			$this->setResponse($msg, null, $e->getCode());
		}
	}

	protected function getHandler()
	{
		if($this->_handler === null)
		{
			$this->_handler = $this->getDefaultHandler();
		}

		return $this->_handler;
	}

	/**
	 * Method wich is called before the record gets validated if a filter 
	 * definition is available
	 *
	 * @param PSX\Data\RecordInterface $record
	 */
	protected function beforeValidate(RecordInterface $record)
	{
	}

	/**
	 * Method wich is called after the record was validated if a filter 
	 * definition is available
	 *
	 * @param PSX\Data\RecordInterface $record
	 */
	protected function afterValidate(RecordInterface $record)
	{
	}

	/**
	 * If we want display an atom feed we need to convert our record to an 
	 * Atom\Record. This method does the mapping. By default we return an entry
	 * that the ATOM format is not supported so you have to overwrite this 
	 * method
	 *
	 * @param PSX\Data\RecordInterface $result
	 * @return PSX\Atom
	 */
	protected function getAtomRecord(RecordInterface $result)
	{
		$msg = new Entry();
		$msg->setId(Uuid::nameBased($this->config['psx_url']));
		$msg->setTitle('ATOM format is not implemented');
		$msg->setUpdated(new DateTime());

		return $msg;
	}

	/**
	 * Returns the filter definition wich validates values
	 *
	 * @return PSX\Filter\FilterDefinition
	 */
	protected function getFilterDefinition()
	{
		return null;
	}

	/**
	 * Returns the handler on wich the API should operate
	 *
	 * @return PSX\Handler\HandlerInterface
	 */
	abstract protected function getDefaultHandler();
}
