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

namespace PSX\Webfinger;

use SimpleXMLElement;
use DateTimeZone;
use PSX\DateTime;

/**
 * Class wich represents an Extensible Resource Descriptor. It offers some
 * methods to easily access the values.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://docs.oasis-open.org/xri/xrd/v1.0/xrd-1.0.html
 */
class Xrd extends SimpleXMLElement
{
	const LINK_REL  = 0x1;
	const LINK_TYPE = 0x2;

	/**
	 * Returns the subject of the xrd or null if no subject is set
	 *
	 * @return string|null
	 */
	public function getSubject()
	{
		if(isset($this->Subject))
		{
			return (string) $this->Subject;
		}

		return null;
	}

	/**
	 * Returns an DateTime object of the expires time if set else null
	 *
	 * @return DateTime|null
	 */
	public function getExpires()
	{
		if(isset($this->Expires))
		{
			$expires = (string) $this->Expires;

			return new DateTime($expires, new DateTimeZone('UTC'));
		}

		return null;
	}

	/**
	 * Searches all <Alias /> tags and returns an array containing all
	 * matched elements.
	 *
	 * @return array<string>
	 */
	public function getAliases()
	{
		$aliases = array();

		if(isset($this->Alias))
		{
			foreach($this->Alias as $alias)
			{
				$aliases[] = (string) $alias;
			}
		}

		return $aliases;
	}

	/**
	 * Searches all <Property /> tags and returns an array containing all
	 * matched elements.
	 *
	 * @return array<SimpleXMLElement>
	 */
	public function getProperties()
	{
		$properties = array();

		if(isset($this->Property))
		{
			foreach($this->Property as $property)
			{
				$properties[] = $property;
			}
		}

		return $properties;
	}

	public function getProperty($key, $value)
	{
		$properties = $this->getProperties();

		foreach($properties as $property)
		{
			if(isset($property[$key]) && $property[$key] == $value)
			{
				return $property;
			}
		}

		return null;
	}

	public function getPropertyByType($type)
	{
		return $this->getProperty('type', $type);
	}

	/**
	 * Returns the value of the first property element wich matches the $type
	 *
	 * @param string $type
	 * @return string
	 */
	public function getPropertyValue($type)
	{
		return (string) $this->getPropertyByType($type);
	}


	/**
	 * Searches all <Link /> tags and returns an array containing all matched
	 * elements.
	 *
	 * @return array<SimpleXMLElement>
	 */
	public function getLinks()
	{
		$links = array();

		if(isset($this->Link))
		{
			foreach($this->Link as $link)
			{
				$links[] = $link;
			}
		}

		return $links;
	}

	public function getLink($key, $value)
	{
		$links = $this->getLinks();

		foreach($links as $link)
		{
			if(isset($link[$key]) && $link[$key] == $value)
			{
				return $link;
			}
		}

		return null;
	}

	public function getLinkByRel($rel)
	{
		return $this->getLink('rel', $rel);
	}

	public function getLinkByType($type)
	{
		return $this->getLink('type', $type);
	}

	/**
	 * Returns the href of the first link element wich matches teh $value.
	 * Depending on teh $type it compares either the rel or type attribute
	 *
	 * @param string $value
	 * @param integer $type
	 * @return string
	 */
	public function getLinkHref($value, $type = 0x1)
	{
		switch($type)
		{
			case self::LINK_REL;

				$element = $this->getLinkByRel($value);
				break;

			case self::LINK_TYPE;

				$element = $this->getLinkByType($value);
				break;
		}

		if(!empty($element) && isset($element['href']))
		{
			return (string) $element['href'];
		}

		return null;
	}
}

