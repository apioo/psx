<?php
/*
 *  $Id: Message.php 488 2012-05-28 12:44:38Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Payment\Paypal\Data;

use PSX\Data\RecordAbstract;

/**
 * Data object wich represents an IPN message send from paypal to the API
 * endpoint
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Payment
 * @version    $Revision: 488 $
 */
class Address extends RecordAbstract
{
	protected $type;
	protected $line1;
	protected $line2;
	protected $city;
	protected $countryCode;
	protected $postalCode;
	protected $state;
	protected $phone;

	public function getName()
	{
		return 'address';
	}

	public function getFields()
	{
		return array(
			'type'           => $this->type,
			'line1'          => $this->line1,
			'line2'          => $this->line2,
			'city'           => $this->city,
			'country_code'   => $this->countryCode,
			'postal_code'    => $this->postalCode,
			'state'          => $this->state,
			'phone'          => $this->phone,
		);
	}

	public function setType($type)
	{
		if(!in_array($type, array('residential', 'business', 'mailbox')))
		{
			throw new Exception('Invalid type');
		}

		$this->type = $type;
	}

	public function setLine1($line1)
	{
		if(strlen($line1) > 100)
		{
			throw new Exception('Max 100 characters');
		}

		$this->line1 = $line1;
	}

	public function setLine2($line2)
	{
		if(strlen($line2) > 100)
		{
			throw new Exception('Max 100 characters');
		}

		$this->line2 = $line2;
	}

	public function setCity($city)
	{
		if(strlen($city) > 50)
		{
			throw new Exception('Max 50 characters');
		}

		$this->city = $city;
	}

	public function setCountryCode($countryCode)
	{
		if(strlen($countryCode) > 2)
		{
			throw new Exception('Max 2 characters');
		}

		$this->countryCode = $countryCode;
	}

	public function setPostalCode($postalCode)
	{
		if(strlen($postalCode) > 20)
		{
			throw new Exception('Max 20 characters');
		}

		$this->postalCode = $postalCode;
	}

	public function setState($state)
	{
		if(strlen($state) > 100)
		{
			throw new Exception('Max 100 characters');
		}

		$this->state = $state;
	}

	public function setPhone($phone)
	{
		if(strlen($phone) > 50)
		{
			throw new Exception('Max 50 characters');
		}

		$this->phone = $phone;
	}
}
