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

namespace PSX\Atom;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Link
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Link extends RecordAbstract
{
	protected $href;
	protected $rel;
	protected $type;
	protected $hreflang;
	protected $title;
	protected $length;

	public function __construct($href = null, $rel = null, $type = null, $hreflang = null, $title = null, $length = null)
	{
		if($href !== null)
		{
			$this->setHref($href);
		}

		if($rel !== null)
		{
			$this->setRel($rel);
		}

		if($type !== null)
		{
			$this->setType($type);
		}

		if($hreflang !== null)
		{
			$this->setHrefLang($hreflang);
		}

		if($title !== null)
		{
			$this->setTitle($title);
		}

		if($length !== null)
		{
			$this->setLength($length);
		}
	}

	public function getRecordInfo()
	{
		return new RecordInfo('link', array(
			'href'     => $this->href,
			'rel'      => $this->rel,
			'type'     => $this->type,
			'hreflang' => $this->hreflang,
			'title'    => $this->title,
			'length'   => $this->length,
		));
	}

	public function setHref($href)
	{
		$this->href = $href;
	}
	
	public function getHref()
	{
		return $this->href;
	}

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

	public function setHrefLang($hreflang)
	{
		$this->hreflang = $hreflang;
	}
	
	public function getHrefLang()
	{
		return $this->hreflang;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	public function getTitle()
	{
		return $this->title;
	}

	public function setLength($length)
	{
		$this->length = $length;
	}
	
	public function getLength()
	{
		return $this->length;
	}
}
