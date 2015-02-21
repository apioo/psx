<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Swagger;

use InvalidArgumentException;
use PSX\Data\RecordAbstract;

/**
 * Operation
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Operation extends RecordAbstract
{
	protected $method;
	protected $nickname;
	protected $summary;
	protected $notes;
	protected $parameters       = array();
	protected $responseMessages = array();

	public function __construct($method = null, $nickname = null, $summary = null)
	{
		$this->nickname = $nickname;
		$this->summary  = $summary;

		if($method !== null)
		{
			$this->setMethod($method);
		}
	}

	public function setMethod($method)
	{
		if(!in_array($method, array('GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS')))
		{
			throw new InvalidArgumentException('Invalid method');
		}

		$this->method = $method;
	}
	
	public function getMethod()
	{
		return $this->method;
	}

	public function setNickname($nickname)
	{
		$this->nickname = $nickname;
	}
	
	public function getNickname()
	{
		return $this->nickname;
	}

	public function setSummary($summary)
	{
		$this->summary = $summary;
	}
	
	public function getSummary()
	{
		return $this->summary;
	}

	public function setNotes($notes)
	{
		$this->notes = $notes;
	}
	
	public function getNotes()
	{
		return $this->notes;
	}

	/**
	 * @param array<PSX\Swagger\ResponseMessage> $responseMessages
	 */
	public function setResponseMessages($responseMessages)
	{
		$this->responseMessages = $responseMessages;
	}
	
	public function getResponseMessages()
	{
		return $this->responseMessages;
	}

	public function addResponseMessage(ResponseMessage $responseMessage)
	{
		$this->responseMessages[] = $responseMessage;
	}

	/**
	 * @param array<PSX\Swagger\Parameter> $parameters
	 */
	public function setParameters($parameters)
	{
		$this->parameters = $parameters;
	}
	
	public function getParameters()
	{
		return $this->parameters;
	}

	public function addParameter(Parameter $parameter)
	{
		$this->parameters[] = $parameter;
	}
}
