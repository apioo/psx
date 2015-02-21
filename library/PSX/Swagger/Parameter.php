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

use PSX\Data\RecordAbstract;
use InvalidArgumentException;

/**
 * Parameter
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Parameter extends RecordAbstract
{
	const TYPE_PATH   = 'path';
	const TYPE_QUERY  = 'query';
	const TYPE_BODY   = 'body';
	const TYPE_HEADER = 'header';
	const TYPE_FORM   = 'form';

	protected $paramType;
	protected $name;
	protected $description;
	protected $required;
	protected $allowMultiple;
	protected $type;

	public function __construct($paramType = null, $name = null, $description = null, $required = null)
	{
		if($paramType !== null)
		{
			$this->setParamType($paramType);
		}

		$this->name        = $name;
		$this->description = $description;
		$this->required    = $required;
	}

	public function setParamType($paramType)
	{
		if(!in_array($paramType, array(self::TYPE_PATH, self::TYPE_QUERY, self::TYPE_BODY, self::TYPE_HEADER, self::TYPE_FORM)))
		{
			throw new InvalidArgumentException('Invalid param type must be one of path, query, body, header, form');
		}

		$this->paramType = $paramType;
	}

	public function getParamType()
	{
		return $this->paramType;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function setRequired($required)
	{
		$this->required = $required;
	}

	public function getRequired()
	{
		return $this->required;
	}

	public function setAllowMultiple($allowMultiple)
	{
		$this->allowMultiple = $allowMultiple;
	}

	public function getAllowMultiple()
	{
		return $this->allowMultiple;
	}

	public function setType($type)
	{
		$this->type = $type;
	}

	public function getType()
	{
		return $this->type;
	}
}
