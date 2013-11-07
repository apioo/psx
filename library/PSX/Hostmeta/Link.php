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

namespace PSX\Hostmeta;

use PSX\Exception;

/**
 * Link
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Link
{
	protected $rel;
	protected $type;
	protected $href;
	protected $template;
	protected $titles;
	protected $properties;

	public function setRel($rel)
	{
		$this->rel = $rel;
	}
	
	public function getRel()
	{
		return $this->rel;
	}

	public function setType($type)
	{
		$this->type = $type;
	}
	
	public function getType()
	{
		return $this->type;
	}

	public function setHref($href)
	{
		$this->href = $href;
	}
	
	public function getHref()
	{
		return $this->href;
	}

	public function setTemplate($template)
	{
		$this->template = $template;
	}
	
	public function getTemplate()
	{
		return $this->template;
	}

	public function setTitles($titles)
	{
		$this->titles = $titles;
	}
	
	public function getTitles()
	{
		return $this->titles;
	}

	public function setProperties($properties)
	{
		$this->properties = $properties;
	}
	
	public function getProperties()
	{
		return $this->properties;
	}
}
