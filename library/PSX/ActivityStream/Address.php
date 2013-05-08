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

	public function getName()
	{
		return 'address';
	}

	public function getFields()
	{
		return array(

			'formatted'     => $this->formatted,
			'streetAddress' => $this->streetAddress,
			'locality'      => $this->locality,
			'region'        => $this->region,
			'postalCode'    => $this->postalCode,
			'country'       => $this->country,

		);
	}

	/**
	 * @param string
	 */
	public function setFormatted($formatted)
	{
		$this->formatted = $formatted;
	}

	/**
	 * @param string
	 */
	public function setStreetAddress($streetAddress)
	{
		$this->streetAddress = $streetAddress;
	}

	/**
	 * @param string
	 */
	public function setLocality($locality)
	{
		$this->locality = $locality;
	}

	/**
	 * @param string
	 */
	public function setRegion($region)
	{
		$this->region = $region;
	}

	/**
	 * @param string
	 */
	public function setPostalCode($postalCode)
	{
		$this->postalCode = $postalCode;
	}

	/**
	 * @param string
	 */
	public function setCountry($country)
	{
		$this->country = $country;
	}
}

