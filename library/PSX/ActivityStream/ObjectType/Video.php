<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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
 * Video
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Video extends Object
{
	protected $embedCode;
	protected $stream;

	public function __construct()
	{
		$this->objectType = 'video';
	}

	public function setEmbedCode($embedCode)
	{
		$this->embedCode = $embedCode;
	}
	
	public function getEmbedCode()
	{
		return $this->embedCode;
	}

	/**
	 * @param PSX\ActivityStream\ObjectFactory $stream
	 */
	public function setStream($stream)
	{
		$this->stream = $stream;
	}
	
	public function getStream()
	{
		return $this->stream;
	}
}
