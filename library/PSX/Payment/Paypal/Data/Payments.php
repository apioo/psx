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

use Countable;
use Iterator;
use PSX\Data\RecordAbstract;

/**
 * Payments
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Payments extends RecordAbstract implements Iterator, Countable
{
	protected $payments = array();

	private $pointer;

	public function getName()
	{
		return 'payments';
	}

	public function getFields()
	{
		return array(
			'payments' => $this->payments,
		);
	}

	public function getPayments()
	{
		return $this->payments;
	}

	/**
	 * @param array<PSX\Payment\Paypal\Data\Payment>
	 */
	public function setPayments($payments)
	{
		$this->payments = $payments;
	}

	public function add(Payment $payment)
	{
		$this->payments[] = $payment;
	}

	public function clear()
	{
		return $this->payments = array();
	}

	// Iterator
	public function current()
	{
		return current($this->payments);
	}

	public function key()
	{
		return key($this->payments);
	}

	public function next()
	{
		return $this->pointer = next($this->payments);
	}

	public function rewind()
	{
		$this->pointer = reset($this->payments);
	}

	public function valid()
	{
		return $this->pointer;
	}

	// Countable
	public function count()
	{
		return count($this->payments);
	}
}
