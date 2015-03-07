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

namespace PSX\ActivityStream\ObjectType;

use PSX\ActivityStream\Object;

/**
 * Binary
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Binary extends Object
{
	protected $compression;
	protected $data;
	protected $fileUrl;
	protected $length;
	protected $md5;
	protected $mimeType;

	public function __construct()
	{
		$this->objectType = 'binary';
	}

	public function setCompression($compression)
	{
		$this->compression = $compression;
	}
	
	public function getCompression()
	{
		return $this->compression;
	}

	public function setData($data)
	{
		$this->data = $data;
	}
	
	public function getData()
	{
		return $this->data;
	}

	public function setFileUrl($fileUrl)
	{
		$this->fileUrl = $fileUrl;
	}
	
	public function getFileUrl()
	{
		return $this->fileUrl;
	}

	/**
	 * @param integer $length
	 */
	public function setLength($length)
	{
		$this->length = $length;
	}
	
	public function getLength()
	{
		return $this->length;
	}

	public function setMd5($md5)
	{
		$this->md5 = $md5;
	}
	
	public function getMd5()
	{
		return $this->md5;
	}

	public function setMimeType($mimeType)
	{
		$this->mimeType = $mimeType;
	}
	
	public function getMimeType()
	{
		return $this->mimeType;
	}
}
