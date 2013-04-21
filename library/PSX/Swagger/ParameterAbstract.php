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

namespace PSX\Swagger;

use PSX\Data\RecordAbstract;

/**
 * PSX_Swagger_ParameterAbstract
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Swagger
 * @version    $Revision: 582 $
 */
abstract class ParameterAbstract extends RecordAbstract
{
	protected $paramType;
	protected $name;
	protected $description;
	protected $dataType;
	protected $required;
	protected $allowMultiple;

	public function __construct($paramType, $name, $description, $dataType, $required = true)
	{
		$this->paramType   = $paramType;
		$this->name        = $name;
		$this->description = $description;
		$this->dataType    = $dataType;
		$this->required    = $required;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}

	public function setDataType($dataType)
	{
		$this->dataType = $dataType;
	}

	public function setRequired($required)
	{
		$this->required = $required;
	}

	public function setAllowMultiple($allowMultiple)
	{
		$this->allowMultiple = $allowMultiple;
	}

	public function getName()
	{
		return $this->paramType;
	}

	public function getFields()
	{
		return array(
			'paramType'   => $this->paramType,
			'name'        => $this->name,
			'description' => $this->description,
			'dataType'    => $this->dataType,
			'required'    => $this->required,
		);
	}

	public static function isScalar($type)
	{
		switch($type)
		{
			case 'byte':
			case 'boolean':
			case 'int':
			case 'long':
			case 'float':
			case 'double':
			case 'string':
			case 'bool':
			case 'integer':
			case 'DateTime':
				return true;
				break;

			default:
				return false;
				break;
		}
	}
}
