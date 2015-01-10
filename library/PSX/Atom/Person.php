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

namespace PSX\Atom;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Person
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Person extends RecordAbstract
{
	protected $name;
	protected $uri;
	protected $email;

	public function __construct($name = null, $uri = null, $email = null)
	{
		if($name !== null)
		{
			$this->setName($name);
		}

		if($uri !== null)
		{
			$this->setUri($uri);
		}

		if($email !== null)
		{
			$this->setEmail($email);
		}
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}
	
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $uri
	 */
	public function setUri($uri)
	{
		$this->uri = $uri;
	}
	
	public function getUri()
	{
		return $this->uri;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}
	
	public function getEmail()
	{
		return $this->email;
	}
}
