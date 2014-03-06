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

namespace PSX\Swagger;

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
	protected $httpMethod;
	protected $nickname;
	protected $responseClass;
	protected $summary;
	protected $notes;

	protected $errorResponses;
	protected $parameters = array();

	public function __construct($httpMethod, $nickname, $responseClass, $summary)
	{
		$this->httpMethod    = $httpMethod;
		$this->nickname      = $nickname;
		$this->responseClass = $responseClass;
		$this->summary       = $summary;
	}

	public function setNickname($nickname)
	{
		$this->nickname = $nickname;
	}

	public function setResponseClass($responseClass)
	{
		$this->responseClass = $responseClass;
	}

	public function setSummary($summary)
	{
		$this->summary = $summary;
	}

	public function setNotes($notes)
	{
		$this->notes = $notes;
	}

	public function addErrorResponse(Error $error)
	{
		$this->errorResponses[] = $errorResponses;
	}

	public function addParameter(ParameterAbstract $parameter)
	{
		$this->parameters[] = $parameter;
	}

	public function getName()
	{
		return 'operation';
	}

	public function getFields()
	{
		return array(
			'httpMethod'     => $this->httpMethod,
			'nickname'       => $this->nickname,
			'responseClass'  => $this->responseClass,
			'summary'        => $this->summary,
			'notes'          => $this->notes,
			'errorResponses' => $this->errorResponses,
			'parameters'     => $this->parameters,
		);;
	}
}
