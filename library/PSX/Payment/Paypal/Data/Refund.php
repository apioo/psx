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
 * Refund
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Refund extends RecordAbstract
{
	protected $id;
	protected $amount;
	protected $createTime;
	protected $state;
	protected $saleId;
	protected $parentPayment;
	protected $updateTime;

	public function getRecordInfo()
	{
		return new RecordInfo('refund', array(
			'id'             => $this->id,
			'amount'         => $this->amount,
			'create_time'    => $this->createTime,
			'state'          => $this->state,
			'sale_id'        => $this->saleId,
			'parent_payment' => $this->parentPayment,
			'update_time'    => $this->updateTime,
		));
	}

	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param PSX\Payment\Paypal\Data\Amount $amount
	 */
	public function setAmount(Amount $amount)
	{
		$this->amount = $amount;
	}
	
	public function getAmount()
	{
		return $this->amount;
	}

	/**
	 * @param DateTime $createTime
	 */
	public function setCreateTime(DateTime $createTime)
	{
		$this->createTime = $createTime;
	}
	
	public function getCreateTime()
	{
		return $this->createTime;
	}

	public function setState($state)
	{
		$this->state = $state;
	}
	
	public function getState()
	{
		return $this->state;
	}

	public function setSaleId($saleId)
	{
		$this->saleId = $saleId;
	}
	
	public function getSaleId()
	{
		return $this->saleId;
	}

	public function setParentPayment($parentPayment)
	{
		$this->parentPayment = $parentPayment;
	}
	
	public function getParentPayment()
	{
		return $this->parentPayment;
	}

	/**
	 * @param DateTime $updateTime
	 */
	public function setUpdateTime(DateTime $updateTime)
	{
		$this->updateTime = $updateTime;
	}
	
	public function getUpdateTime()
	{
		return $this->updateTime;
	}
}
