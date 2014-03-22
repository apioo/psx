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

	public function __construct($paramType = null, $name = null, $description = null, $required = null, $allowMultiple = null)
	{
		$this->name          = $name;
		$this->description   = $description;
		$this->required      = $required;
		$this->allowMultiple = $allowMultiple;

		if($paramType !== null)
		{
			$this->setParamType($paramType);
		}
	}

	public function setParamType($paramType)
	{
		if(!in_array($paramType, array(self::TYPE_PATH, self::TYPE_QUERY, self::TYPE_BODY, self::TYPE_HEADER, self::TYPE_FORM)))
		{
			throw new InvalidArgumentException('Invalid param type must be one of path, query, body, header, form');
		}

		$this->paramType = $paramType;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}

	public function setRequired($required)
	{
		$this->required = $required;
	}

	public function setAllowMultiple($allowMultiple)
	{
		$this->allowMultiple = $allowMultiple;
	}
}
