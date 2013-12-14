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

namespace PSX\Rss;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Enclosure
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Enclosure extends RecordAbstract
{
	protected $url;
	protected $length;
	protected $type;

	public function __construct($url = null, $length = null, $type = null)
	{
		if($url !== null)
		{
			$this->setUrl($url);
		}

		if($length !== null)
		{
			$this->setLength($length);
		}

		if($type !== null)
		{
			$this->setType($type);
		}
	}

	public function getRecordInfo()
	{
		return new RecordInfo('enclosure', array(
			'url'    => $this->url,
			'length' => $this->length,
			'type'   => $this->type,
		));
	}

	public function setUrl($url)
	{
		$this->url = $url;
	}
	
	public function getUrl()
	{
		return $this->url;
	}

	public function setLength($length)
	{
		$this->length = $length;
	}
	
	public function getLength()
	{
		return $this->length;
	}

	public function setType($type)
	{
		$this->type = $type;
	}
	
	public function getType()
	{
		return $this->type;
	}
}
