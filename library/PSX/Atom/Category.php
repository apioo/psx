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
 * Category
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Category extends RecordAbstract
{
	protected $term;
	protected $scheme;
	protected $label;

	public function __construct($term = null, $scheme = null, $label = null)
	{
		if($term !== null)
		{
			$this->setTerm($term);
		}

		if($scheme !== null)
		{
			$this->setScheme($scheme);
		}

		if($label !== null)
		{
			$this->setLabel($label);
		}
	}

	/**
	 * @param string $term
	 */
	public function setTerm($term)
	{
		$this->term = $term;
	}
	
	public function getTerm()
	{
		return $this->term;
	}

	/**
	 * @param string $scheme
	 */
	public function setScheme($scheme)
	{
		$this->scheme = $scheme;
	}
	
	public function getScheme()
	{
		return $this->scheme;
	}

	/**
	 * @param string $label
	 */
	public function setLabel($label)
	{
		$this->label = $label;
	}
	
	public function getLabel()
	{
		return $this->label;
	}
}
