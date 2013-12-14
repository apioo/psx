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
use PSX\Data\RecordInfo;

/**
 * Item
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Item extends RecordAbstract
{
	protected $quantity;
	protected $name;
	protected $price;
	protected $currency;
	protected $sku;

	public function getRecordInfo()
	{
		return new RecordInfo('item', array(
			'quantity' => $this->quantity,
			'name'     => $this->name,
			'price'    => $this->price,
			'currency' => $this->currency,
			'sku'      => $this->sku,
		));
	}

	public function getQuantity()
	{
		return $this->quantity;
	}

	public function setQuantity($quantity)
	{
		if(strlen($quantity) > 10)
		{
			throw new Exception('Max 10 characters');
		}

		$this->quantity = $quantity;
	}

	public function getItemName()
	{
		return $this->name;
	}

	public function setItemName($name)
	{
		if(strlen($name) > 127)
		{
			throw new Exception('Max 127 characters');
		}

		$this->name = $name;
	}

	public function getPrice()
	{
		return $this->price;
	}

	public function setPrice($price)
	{
		if(strlen($name) > 10)
		{
			throw new Exception('Max 10 characters');
		}

		$this->price = $price;
	}

	public function getCurrency()
	{
		return $this->currency;
	}

	public function setCurrency($currency)
	{
		if(!in_array($currency, Amount::$currencyCodes))
		{
			throw new Exception('Invalid currency');
		}

		$this->currency = $currency;
	}

	public function getSku()
	{
		return $this->sku;
	}

	public function setSku($sku)
	{
		$this->sku = $sku;
	}
}
