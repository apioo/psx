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

namespace PSX\Atom;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Text
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Text extends RecordAbstract
{
	protected $type;
	protected $content;

	public function __construct($content = null, $type = null)
	{
		if($content !== null)
		{
			$this->setContent($content);
		}

		if($type !== null)
		{
			$this->setType($type);
		}
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}
	
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $content
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}
	
	public function getContent()
	{
		return $this->content;
	}

	public function __toString()
	{
		return $this->content;
	}
}
