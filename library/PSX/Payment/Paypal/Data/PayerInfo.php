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

namespace PSX\Payment\Paypal\Data;

use PSX\Data\RecordAbstract;

/**
 * PayerInfo
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class PayerInfo extends RecordAbstract
{
	protected $email;
	protected $firstName;
	protected $lastName;
	protected $payerId;
	protected $phone;
	protected $shippingAddress;

	public function getName()
	{
		return 'payerInfo';
	}

	public function getFields()
	{
		return array(
			'email'            => $this->email,
			'first_name'       => $this->firstName,
			'last_name'        => $this->lastName,
			'payer_id'         => $this->payerId,
			'phone'            => $this->phone,
			'shipping_address' => $this->shippingAddress,
		);
	}

	public function setEmail($email)
	{
		$this->email = $email;
	}

	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;
	}

	public function setLastName($lastName)
	{
		$this->lastName = $lastName;
	}

	public function setPayerId($payerId)
	{
		$this->payerId = $payerId;
	}

	public function setPhone($phone)
	{
		$this->phone = $phone;
	}

	public function setShippingAddress(ShippingAddress $shippingAddress)
	{
		$this->shippingAddress = $shippingAddress;
	}
}
