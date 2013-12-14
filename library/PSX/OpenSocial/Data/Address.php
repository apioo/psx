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

namespace PSX\OpenSocial\Data;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Address
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Address extends RecordAbstract
{
	protected $country;
	protected $formatted;
	protected $latitude;
	protected $locality;
	protected $longitude;
	protected $postalCode;
	protected $region;
	protected $streetAddress;
	protected $type;

	public function getRecordInfo()
	{
		return new RecordInfo('address', array(
			'country'       => $this->country,
			'formatted'     => $this->formatted,
			'latitude'      => $this->latitude,
			'locality'      => $this->locality,
			'longitude'     => $this->longitude,
			'postalCode'    => $this->postalCode,
			'region'        => $this->region,
			'streetAddress' => $this->streetAddress,
			'type'          => $this->type,
		));
	}

	/**
	 * @param string
	 */
	public function setCountry($country)
	{
		$this->country = $country;
	}
	
	public function getCountry()
	{
		return $this->country;
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
	public function setLatitude($latitude)
	{
		$this->latitude = $latitude;
	}
	
	public function getLatitude()
	{
		return $this->latitude;
	}

	/**
	 * @param string
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
	 * @param string
	 */
	public function setLongitude($longitude)
	{
		$this->longitude = $longitude;
	}
	
	public function getLongitude()
	{
		return $this->longitude;
	}

	/**
	 * @param string
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
	 * @param string
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
	 * @param string
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
	 * @param string
	 */
	public function setType($type)
	{
		$this->type = $type;
	}
	
	public function getType()
	{
		return $this->type;
	}
}

