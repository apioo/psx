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

namespace PSX\ActivityStream\ObjectType;

use PSX\ActivityStream\Object;

/**
 * Binary
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
