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

namespace PSX\Validate;

/**
 * Result
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Result
{
	protected $value;
	protected $errors;

	public function __construct($value = null, array $errors = array())
	{
		$this->value  = $value;
		$this->errors = $errors;
	}

	public function setValue($value)
	{
		$this->value = $value;
	}
	
	public function getValue()
	{
		return $this->value;
	}

	public function setErrors(array $errors)
	{
		$this->errors = $errors;
	}
	
	public function getErrors()
	{
		return $this->errors;
	}

	public function addError($message)
	{
		$this->errors[] = $message;
	}

	public function getFirstError()
	{
		return isset($this->errors[0]) ? $this->errors[0] : null;
	}

	public function isSuccessful()
	{
		return count($this->errors) == 0;
	}

	public function hasError()
	{
		return count($this->errors) > 0;
	}

	public function __toString()
	{
		return implode(', ', $this->errors);
	}
}
