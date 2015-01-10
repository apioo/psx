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

namespace PSX\ActivityStream;

use PSX\Data\RecordAbstract;

/**
 * Address
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Address extends RecordAbstract
{
	protected $formatted;
	protected $streetAddress;
	protected $locality;
	protected $region;
	protected $postalCode;
	protected $country;

	/**
	 * @param string $formatted
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
	 * @param string $streetAddress
	 */
	public function setStreetAddress($streetAddress)
	{
		$this->streetAddress = $streetAddress;
	}

	public function getStreetAddress()
	{
		return $this->streetAddress;
	}

	/**
	 * @param string $locality
	 */
	public function setLocality($locality)
	{
		$this->locality = $locality;
	}

	public function getLocality()
	{
		return $this->locality;
	}

	/**
	 * @param string $region
	 */
	public function setRegion($region)
	{
		$this->region = $region;
	}

	public function getRegion()
	{
		return $this->region;
	}

	/**
	 * @param string $postalCode
	 */
	public function setPostalCode($postalCode)
	{
		$this->postalCode = $postalCode;
	}

	public function getPostalCode()
	{
		return $this->postalCode;
	}

	/**
	 * @param string $country
	 */
	public function setCountry($country)
	{
		$this->country = $country;
	}

	public function getCountry()
	{
		return $this->country;
	}
}

