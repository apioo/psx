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
class CreditCard extends RecordAbstract
{
	protected $id;
	protected $number;
	protected $type;
	protected $expireMonth;
	protected $expireYear;
	protected $cvv2;
	protected $firstName;
	protected $lastName;
	protected $billingAddress;
	protected $state;
	protected $validUntil;

	public function getName()
	{
		return 'creditCard';
	}

	public function getFields()
	{
		return array(
			'id'              => $this->id,
			'number'          => $this->number,
			'type'            => $this->type,
			'expire_month'    => $this->expireMonth,
			'expire_year'     => $this->expireYear,
			'cvv2'            => $this->cvv2,
			'first_name'      => $this->firstName,
			'last_name'       => $this->lastName,
			'billing_address' => $this->billingAddress,
			'state'           => $this->state,
			'valid_until'     => $this->validUntil,
		);
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function setNumber($number)
	{
		$this->number = $number;
	}

	public function setType($type)
	{
		if(!in_array($type, array('Visa', 'MasterCard', 'Discover', 'Amex')))
		{
			throw new Exception('Invalid type');
		}

		$this->type = $type;
	}

	public function setExpireMonth($expireMonth)
	{
		if(strlen($expireMonth) > 2)
		{
			throw new Exception('Max 2 characters');
		}

		$this->expireMonth = $expireMonth;
	}

	public function setExpireYear($expireYear)
	{
		if(strlen($expireYear) != 4)
		{
			throw new Exception('Must have 4 characters');
		}

		$this->expireYear = $expireYear;
	}

	public function setCvv2($cvv2)
	{
		if(strlen($cvv2) > 4)
		{
			throw new Exception('Max 4 characters');
		}

		$this->cvv2 = $cvv2;
	}

	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;
	}

	public function setLastName($lastName)
	{
		$this->lastName = $lastName;
	}

	public function setBillingAddress(Address $billingAddress)
	{
		$this->billingAddress = $billingAddress;
	}

	public function setState($state)
	{
		if(!in_array($state, array('expired', 'ok')))
		{
			throw new Exception('Invalid state');
		}

		$this->state = $state;
	}

	public function setValidUntil($validUntil)
	{
		$this->validUntil = $validUntil;
	}
}
