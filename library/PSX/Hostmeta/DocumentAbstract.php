<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Hostmeta;

use DateTime;

/**
 * Document
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class DocumentAbstract
{
	protected $subject;
	protected $expires;
	protected $aliases;
	protected $properties = array();
	protected $links = array();

	public function __construct()
	{
	}

	public function setSubject($subject)
	{
		$this->subject = $subject;
	}
	
	public function getSubject()
	{
		return $this->subject;
	}

	public function setExpires(DateTime $expires)
	{
		$this->expires = $expires;
	}

	public function getExpires()
	{
		return $this->expires;
	}

	public function setAliases(array $aliases)
	{
		$this->aliases = $aliases;
	}
	
	public function getAliases()
	{
		return $this->aliases;
	}

	public function setProperties(array $properties)
	{
		$this->properties = $properties;
	}
	
	public function getProperties()
	{
		return $this->properties;
	}

	public function addProperty($name, $value)
	{
		$this->properties[$name] = $value;
	}

	public function getProperty($name)
	{
		if(!empty($this->properties))
		{
			foreach($this->properties as $propertyName => $value)
			{
				if($propertyName == $name)
				{
					return $value;
				}
			}
		}

		return null;
	}

	public function setLinks(array $links)
	{
		$this->links = $links;
	}
	
	public function getLinks()
	{
		return $this->links;
	}

	public function addLink(Link $link)
	{
		$this->links[] = $link;
	}

	public function getLinkByRel($rel)
	{
		if(!empty($this->links))
		{
			foreach($this->links as $link)
			{
				if($link->getRel() == $rel)
				{
					return $link;
				}
			}
		}

		return null;
	}

	//abstract public function import($data);
	abstract public function export();
}
