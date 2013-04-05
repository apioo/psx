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

use DateTime;
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

	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @param PSX\Payment\Paypal\Data\Amount $amount
	 */
	public function setAmount(Amount $amount)
	{
		$this->amount = $amount;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * @param DateTime $createTime
	 */
	public function setCreateTime(DateTime $createTime)
	{
		$this->createTime = $createTime;
	}

	public function setState($state)
	{
		if(!in_array($state, array('pending', 'completed', 'refunded', 'partially_refunded')))
		{
			throw new Exception('Invalid state');
		}

		$this->state = $state;
	}

	public function setSaleId($saleId)
	{
		$this->saleId = $saleId;
	}

	public function setParentPayment($parentPayment)
	{
		$this->parentPayment = $parentPayment;
	}

	/**
	 * @param DateTime $updateTime
	 */
	public function setUpdateTime(DateTime $updateTime)
	{
		$this->updateTime = $updateTime;
	}
}
