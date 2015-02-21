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

namespace PSX\Api\View\Generator\Wsdl;

/**
 * Operation
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Operation
{
	protected $name;
	protected $method;
	protected $in;
	protected $out;

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setMethod($method)
	{
		$this->method = $method;
	}
	
	public function getMethod()
	{
		return $this->method;
	}

	public function setIn($in)
	{
		$this->in = $in;
	}

	public function getIn()
	{
		return $this->in;
	}

	public function hasIn()
	{
		return !empty($this->in);
	}

	public function setOut($out)
	{
		$this->out = $out;
	}
	
	public function getOut()
	{
		return $this->out;
	}

	public function hasOut()
	{
		return !empty($this->out);
	}

	public function hasOperation()
	{
		return !empty($this->in) || !empty($this->out);
	}

	public function isInOnly()
	{
		return !empty($this->in) && empty($this->out);
	}

	public function isOutOnly()
	{
		return empty($this->in) && !empty($this->out);
	}

	public function isInOut()
	{
		return !empty($this->in) && !empty($this->out);
	}
}
