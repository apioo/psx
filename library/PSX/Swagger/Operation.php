<?php
/*
 *  $Id: Condition.php 582 2012-08-15 21:27:02Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

/**
 * PSX_Swagger_Operation
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Swagger
 * @version    $Revision: 582 $
 */
class PSX_Swagger_Operation extends PSX_Data_RecordAbstract
{
	private $httpMethod;
	private $nickname;
	private $responseClass;
	private $summary;
	private $notes;

	private $errorResponses;
	private $parameters = array();

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

	public function addErrorResponse(PSX_Swagger_Error $error)
	{
		$this->errorResponses[] = $errorResponses;
	}

	public function addParameter(PSX_Swagger_ParameterAbstract $parameter)
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
