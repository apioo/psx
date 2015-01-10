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
 * Generator
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
