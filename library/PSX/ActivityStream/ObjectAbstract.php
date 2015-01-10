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

namespace PSX\ActivityStream;

use PSX\Data\RecordAbstract;

/**
 * ObjectAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ObjectAbstract extends RecordAbstract
{
	protected $id;
	protected $objectType;
	protected $language;
	protected $displayName;
	protected $url;
	protected $rel;
	protected $mediaType;

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setObjectType($objectType)
	{
		$this->objectType = $objectType;
	}

	public function getObjectType()
	{
		return $this->objectType;
	}

	public function setLanguage($language)
	{
		$this->language = $language;
	}
	
	public function getLanguage()
	{
		return $this->language;
	}

	public function setDisplayName($displayName)
	{
		$this->displayName = $displayName;
	}
	
	public function getDisplayName()
	{
		return $this->displayName;
	}

	/**
	 * @param PSX\ActivityStream\ObjectFactory $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}
	
	public function getUrl()
	{
		return $this->url;
	}

	public function setRel($rel)
	{
		$this->rel = $rel;
	}
	
	public function getRel()
	{
		return $this->rel;
	}

	public function setMediaType($mediaType)
	{
		$this->mediaType = $mediaType;
	}
	
	public function getMediaType()
	{
		return $this->mediaType;
	}

	public function __toString()
	{
		return $this->url;
	}
}
