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

namespace PSX\Atom;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Generator
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Generator extends RecordAbstract
{
	protected $text;
	protected $uri;
	protected $version;

	public function __construct($text = null, $uri = null, $version = null)
	{
		if($text !== null)
		{
			$this->setText($text);
		}

		if($uri !== null)
		{
			$this->setUri($uri);
		}

		if($version !== null)
		{
			$this->setVersion($version);
		}
	}

	/**
	 * @param string $text
	 */
	public function setText($text)
	{
		$this->text = $text;
	}
	
	public function getText()
	{
		return $this->text;
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
	 * @param string $version
	 */
	public function setVersion($version)
	{
		$this->version = $version;
	}
	
	public function getVersion()
	{
		return $this->version;
	}

	public function __toString()
	{
		return $this->text;
	}
}
