<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\ActivityStream;

use PSX\Data\RecordAbstract;

/**
 * ObjectAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
	 * @param \PSX\ActivityStream\ObjectFactory $url
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
