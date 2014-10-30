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

use DateTime;
use PSX\Data\Message;
use PSX\Data\Record\Mapper;
use PSX\Data\Record\Mapper\Rule;
use PSX\Data\RecordInterface;
use PSX\Data\WriterInterface;
use PSX\Exception;
use PSX\Handler\HandlerManipulationInterface;
use PSX\Handler\HandlerQueryInterface;
use PSX\Util\Uuid;
use PSX\Util\Api\FilterParameter;
use PSX\Sql\Condition;
use PSX\Validate\ValidatorInterface;
use PSX\Http\Exception as StatusCode;

/**
 * This controller simplifies creating an API from an handler
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link	http://phpsx.org
 */
abstract class HandlerApiAbstract extends ApiAbstract
{
	private $_handler;

	public function onGet()
	{
		$this->_handler = $this->getHandler();

		if(!$this->_handler instanceof HandlerQueryInterface)
		{
			throw new StatusCode\MethodNotAllowedException('Method is not allowed', array('POST', 'PUT', 'DELETE'));
		}

		$parameter = $this->getFilterParameter();
		$condition = FilterParameter::getCondition($parameter);

		$result = $this->doGet($parameter->getStartIndex(), 
				$parameter->getCount(), 
				$parameter->getSortBy(), 
				$parameter->getSortOrder(), 
				$condition);

		$this->setBody($result);
	}

	public function onPost()
	{
		$this->_handler = $this->getHandler();

		if(!$this->_handler instanceof HandlerManipulationInterface)
		{
			throw new StatusCode\MethodNotAllowedException('Method is not allowed', array('GET'));
		}

		$record = $this->import($this->_handler->getRecord());

		$this->beforeValidate($record);

		// validate
		$validator = $this->getValidator();

		if($validator instanceof ValidatorInterface)
		{
			$validator->validate($record);
		}

		$this->afterValidate($record);

		// insert
		$this->beforeCreate($record);

		$this->doCreate($record);

		$this->afterCreate($record);

		// message
		$message = new Message('You have successful create a ' . $record->getRecordInfo()->getName(), true);

		$this->setBody($message);
	}

	public function onPut()
	{
		$this->_handler = $this->getHandler();

		if(!$this->_handler instanceof HandlerManipulationInterface)
		{
			throw new StatusCode\MethodNotAllowedException('Method is not allowed', array('GET'));
		}

		$record = $this->import($this->_handler->getRecord());

		$this->beforeValidate($record);

		// validate
		$validator = $this->getValidator();

		if($validator instanceof ValidatorInterface)
		{
			$validator->validate($record);
		}

		$this->afterValidate($record);

		// update
		$this->beforeUpdate($record);

		$this->doUpdate($record);

		$this->afterUpdate($record);

		// message
		$message = new Message('You have successful update a ' . $record->getRecordInfo()->getName(), true);

		$this->setBody($message);
	}

	public function onDelete()
	{
		$this->_handler = $this->getHandler();
		
		if(!$this->_handler instanceof HandlerManipulationInterface)
		{
			throw new StatusCode\MethodNotAllowedException('Method is not allowed', array('GET'));
		}

		$record = $this->import($this->_handler->getRecord());

		$this->beforeValidate($record);

		// validate
		$validator = $this->getValidator();

		if($validator instanceof ValidatorInterface)
		{
			$validator->validate($record);
		}

		$this->afterValidate($record);

		// delete
		$this->beforeDelete($record);

		$this->doDelete($record);

		$this->afterDelete($record);

		// message
		$message = new Message('You have successful delete a ' . $record->getRecordInfo()->getName(), true);

		$this->setBody($message);
	}

	protected function doGet($startIndex = null, $count = null, $sortBy = null, $sortOrder = null, Condition $condition = null)
	{
		return $this->_handler->getCollection($startIndex, 
					$count, 
					$sortBy, 
					$sortOrder, 
					$condition);
	}

	protected function doCreate(RecordInterface $record)
	{
		$this->_handler->create($record);
	}

	protected function doUpdate(RecordInterface $record)
	{
		$this->_handler->update($record);
	}

	protected function doDelete(RecordInterface $record)
	{
		$this->_handler->delete($record);
	}

	/**
	 * Method which is called before the record gets created
	 *
	 * @param PSX\Data\RecordInterface $record
	 */
	protected function beforeCreate(RecordInterface $record)
	{
	}

	/**
	 * Method which is called after the record was created
	 *
	 * @param PSX\Data\RecordInterface $record
	 */
	protected function afterCreate(RecordInterface $record)
	{
	}

	/**
	 * Method which is called before the record gets updated
	 *
	 * @param PSX\Data\RecordInterface $record
	 */
	protected function beforeUpdate(RecordInterface $record)
	{
	}

	/**
	 * Method which is called after the record was updated
	 *
	 * @param PSX\Data\RecordInterface $record
	 */
	protected function afterUpdate(RecordInterface $record)
	{
	}

	/**
	 * Method which is called before the record gets deleted
	 *
	 * @param PSX\Data\RecordInterface $record
	 */
	protected function beforeDelete(RecordInterface $record)
	{
	}

	/**
	 * Method which is called after the record was deleted
	 *
	 * @param PSX\Data\RecordInterface $record
	 */
	protected function afterDelete(RecordInterface $record)
	{
	}

	/**
	 * Method which is called before the record gets validated
	 *
	 * @param PSX\Data\RecordInterface $record
	 */
	protected function beforeValidate(RecordInterface $record)
	{
	}

	/**
	 * Method which is called after the record was validated
	 *
	 * @param PSX\Data\RecordInterface $record
	 */
	protected function afterValidate(RecordInterface $record)
	{
	}

	/**
	 * Returns the filter definition which validates values
	 *
	 * @return PSX\Validate\ValidatorInterface
	 */
	protected function getValidator()
	{
		return null;
	}

	/**
	 * Returns the handler on which the API should operate
	 *
	 * @return PSX\Handler\HandlerInterface
	 */
	abstract protected function getHandler();
}
