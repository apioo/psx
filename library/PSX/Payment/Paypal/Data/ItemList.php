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
 * ItemList
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ItemList extends RecordAbstract
{
	protected $items;
	protected $shippingAddress;

	public function getName()
	{
		return 'itemList';
	}

	public function getFields()
	{
		return array(
			'items'            => $this->items,
			'shipping_address' => $this->shippingAddress,
		);
	}

	public function getItems()
	{
		return $this->items;
	}

	/**
	 * @param array<PSX\Payment\Paypal\Data\Item> $items
	 */
	public function setItems($items)
	{
		$this->items = $items;
	}

	public function addItem(Item $item)
	{
		$this->items[] = $item;
	}

	public function clearItems()
	{
		$this->items = array();
	}

	public function getShippingAddress()
	{
		return $this->shippingAddress;
	}

	/**
	 * @param PSX\Payment\Paypal\Data\ShippingAddress $shippingAddress
	 */
	public function setShippingAddress(ShippingAddress $shippingAddress)
	{
		$this->shippingAddress = $shippingAddress;
	}
}
