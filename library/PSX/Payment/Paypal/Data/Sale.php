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

use DateTime;
use PSX\Data\RecordAbstract;

/**
 * Sale
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Sale extends RecordAbstract
{
	protected $id;
	protected $amount;
	protected $description;
	protected $createTime;
	protected $state;
	protected $saleId;
	protected $parentPayment;
	protected $updateTime;

	public function getName()
	{
		return 'sale';
	}

	public function getFields()
	{
		return array(
			'id'             => $this->id,
			'amount'         => $this->amount,
			'description'    => $this->description,
			'create_time'    => $this->createTime,
			'state'          => $this->state,
			'sale_id'        => $this->saleId,
			'parent_payment' => $this->parentPayment,
			'update_time'    => $this->updateTime,
		);
	}

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getAmount()
	{
		return $this->amount;
	}

	/**
	 * @param PSX\Payment\Paypal\Data\Amount $amount
	 */
	public function setAmount(Amount $amount)
	{
		$this->amount = $amount;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}

	public function getCreateTime()
	{
		return $this->createTime;
	}

	/**
	 * @param DateTime $createTime
	 */
	public function setCreateTime(DateTime $createTime)
	{
		$this->createTime = $createTime;
	}

	public function getState()
	{
		return $this->state;
	}

	public function setState($state)
	{
		if(!in_array($state, array('pending', 'completed', 'refunded', 'partially_refunded')))
		{
			throw new Exception('Invalid state');
		}

		$this->state = $state;
	}

	public function getSaleId()
	{
		return $this->saleId;
	}

	public function setSaleId($saleId)
	{
		$this->saleId = $saleId;
	}

	public function getParentPayment()
	{
		return $this->parentPayment;
	}

	public function setParentPayment($parentPayment)
	{
		$this->parentPayment = $parentPayment;
	}

	public function getUpdateTime()
	{
		return $this->updateTime;
	}

	/**
	 * @param DateTime $updateTime
	 */
	public function setUpdateTime(DateTime $updateTime)
	{
		$this->updateTime = $updateTime;
	}
}
