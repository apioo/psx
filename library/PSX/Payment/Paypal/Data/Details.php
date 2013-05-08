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
 * Details
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Details extends RecordAbstract
{
	protected $shipping;
	protected $subtotal;
	protected $tax;
	protected $fee;

	public function getName()
	{
		return 'details';
	}

	public function getFields()
	{
		return array(
			'shipping' => $this->shipping,
			'subtotal' => $this->subtotal,
			'tax'      => $this->tax,
			'fee'      => $this->fee,
		);
	}

	public function setShipping($shipping)
	{
		$shipping = (float) $shipping;
		$shipping = round($shipping, 2);

		if(strlen($shipping) > 10)
		{
			throw new Exception('Max 10 characters');
		}

		$this->shipping = $shipping;
	}

	public function setSubtotal($subtotal)
	{
		$subtotal = (float) $subtotal;
		$subtotal = round($subtotal, 2);

		if(strlen($subtotal) > 10)
		{
			throw new Exception('Max 10 characters');
		}

		$this->subtotal = $subtotal;
	}

	public function setTax($tax)
	{
		$tax = (float) $tax;
		$tax = round($tax, 2);

		if(strlen($tax) > 10)
		{
			throw new Exception('Max 10 characters');
		}

		$this->tax = $tax;
	}

	public function setFee($fee)
	{
		if(strlen($fee) > 10)
		{
			throw new Exception('Max 10 characters');
		}

		$this->fee = $fee;
	}
}
