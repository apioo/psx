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

namespace PSX\Payment\Skrill\Data;

use PSX\Data\RecordAbstract;

/**
 * Customer
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Payment
 * @version    $Revision: 488 $
 */
class Customer extends RecordAbstract
{
	protected $payFromEmail;
	protected $title;
	protected $firstname;
	protected $lastname;
	protected $dateOfBirth;
	protected $address;
	protected $address2;
	protected $phoneNumber;
	protected $postalCode;
	protected $city;
	protected $state;
	protected $country;

	public function getName()
	{
		return 'customer';
	}

	public function getFields()
	{
		return array(
			'pay_from_email' => $this->payFromEmail,
			'title'          => $this->title,
			'firstname'      => $this->firstname,
			'lastname'       => $this->lastname,
			'date_of_birth'  => $this->dateOfBirth,
			'address'        => $this->address,
			'address2'       => $this->address2,
			'phone_number'   => $this->phoneNumber,
			'postal_code'    => $this->postalCode,
			'city'           => $this->city,
			'state'          => $this->state,
			'country'        => $this->country,
		);
	}

	public function setPayFromEmail($payFromEmail)
	{
		$this->payFromEmail = $payFromEmail;
	}
	
	public function getPayFromEmail()
	{
		return $this->payFromEmail;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	public function getTitle()
	{
		return $this->title;
	}

	public function setFirstname($firstname)
	{
		$this->firstname = $firstname;
	}
	
	public function getFirstname()
	{
		return $this->firstname;
	}

	public function setLastname($lastname)
	{
		$this->lastname = $lastname;
	}
	
	public function getLastname()
	{
		return $this->lastname;
	}

	public function setDateOfBirth($dateOfBirth)
	{
		$this->dateOfBirth = $dateOfBirth;
	}
	
	public function getDateOfBirth()
	{
		return $this->dateOfBirth;
	}

	public function setAddress($address)
	{
		$this->address = $address;
	}
	
	public function getAddress()
	{
		return $this->address;
	}

	public function setAddress2($address2)
	{
		$this->address2 = $address2;
	}
	
	public function getAddress2()
	{
		return $this->address2;
	}

	public function setPhoneNumber($phoneNumber)
	{
		$this->phoneNumber = $phoneNumber;
	}
	
	public function getPhoneNumber()
	{
		return $this->phoneNumber;
	}

	public function setPostalCode($postalCode)
	{
		$this->postalCode = $postalCode;
	}
	
	public function getPostalCode()
	{
		return $this->postalCode;
	}

	public function setCity($city)
	{
		$this->city = $city;
	}
	
	public function getCity()
	{
		return $this->city;
	}

	public function setState($state)
	{
		$this->state = $state;
	}
	
	public function getState()
	{
		return $this->state;
	}

	public function setCountry($country)
	{
		$this->country = $country;
	}
	
	public function getCountry()
	{
		return $this->country;
	}
}
