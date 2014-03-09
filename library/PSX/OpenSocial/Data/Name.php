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

namespace PSX\OpenSocial\Data;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Name
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Name extends RecordAbstract
{
	protected $familyName;
	protected $formatted;
	protected $givenName;
	protected $honorificPrefix;
	protected $honorificSuffix;
	protected $middleName;
	protected $pronunciation;
	protected $pronunciationUrl;

	/**
	 * @param string
	 */
	public function setFamilyName($familyName)
	{
		$this->familyName = $familyName;
	}
	
	public function getFamilyName()
	{
		return $this->familyName;
	}

	/**
	 * @param string
	 */
	public function setFormatted($formatted)
	{
		$this->formatted = $formatted;
	}
	
	public function getFormatted()
	{
		return $this->formatted;
	}

	/**
	 * @param string
	 */
	public function setGivenName($givenName)
	{
		$this->givenName = $givenName;
	}
	
	public function getGivenName()
	{
		return $this->givenName;
	}

	/**
	 * @param string
	 */
	public function setHonorificPrefix($honorificPrefix)
	{
		$this->honorificPrefix = $honorificPrefix;
	}
	
	public function getHonorificPrefix()
	{
		return $this->honorificPrefix;
	}

	/**
	 * @param string
	 */
	public function setHonorificSuffix($honorificSuffix)
	{
		$this->honorificSuffix = $honorificSuffix;
	}
	
	public function getHonorificSuffix()
	{
		return $this->honorificSuffix;
	}

	/**
	 * @param string
	 */
	public function setMiddleName($middleName)
	{
		$this->middleName = $middleName;
	}
	
	public function getMiddleName()
	{
		return $this->middleName;
	}

	/**
	 * @param string
	 */
	public function setPronunciation($pronunciation)
	{
		$this->pronunciation = $pronunciation;
	}
	
	public function getPronunciation()
	{
		return $this->pronunciation;
	}

	/**
	 * @param string
	 */
	public function setPronunciationUrl($pronunciationUrl)
	{
		$this->pronunciationUrl = $pronunciationUrl;
	}
	
	public function getPronunciationUrl()
	{
		return $this->pronunciationUrl;
	}
}

