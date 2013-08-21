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
use DateTime;

/**
 * Payment
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Payment extends RecordAbstract
{
	protected $intent;
	protected $payer;
	protected $transactions;
	protected $redirectUrls;
	protected $id;
	protected $createTime;
	protected $state;
	protected $updateTime;
	protected $links;

	public function getName()
	{
		return 'payment';
	}

	public function getFields()
	{
		return array(
			'intent'        => $this->intent,
			'payer'         => $this->payer,
			'redirect_urls' => $this->redirectUrls,
			'transactions'  => $this->transactions,
			'id'            => $this->id,
			'create_time'   => $this->createTime,
			'state'         => $this->state,
			'update_time'   => $this->updateTime,
			'links'         => $this->links,
		);
	}

	public function getIntent()
	{
		return $this->intent;
	}

	public function setIntent($intent)
	{
		if(!in_array($intent, array('sale')))
		{
			throw new Exception('Invalid intent');
		}

		$this->intent = $intent;
	}

	public function getPayer()
	{
		return $this->payer;
	}

	/**
	 * @param PSX\Payment\Paypal\Data\Payer $payer
	 */
	public function setPayer(Payer $payer)
	{
		$this->payer = $payer;
	}

	public function getRedirectUrls()
	{
		return $this->redirectUrls;
	}

	/**
	 * @param PSX\Payment\Paypal\Data\RedirectUrls $redirectUrls
	 */
	public function setRedirectUrls(RedirectUrls $redirectUrls)
	{
		$this->redirectUrls = $redirectUrls;
	}

	public function getTransactions()
	{
		return $this->transactions;
	}

	/**
	 * @param array<PSX\Payment\Paypal\Data\Transaction> $transactions
	 */
	public function setTransactions(array $transactions)
	{
		$this->transactions = $transactions;
	}

	public function addTransaction(Transaction $transaction)
	{
		$this->transactions[] = $transaction;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
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
		$this->state = $state;
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

	public function getLinks()
	{
		return $this->links;
	}

	/**
	 * @param array<PSX\Payment\Paypal\Data\Link> $links
	 */
	public function setLinks($links)
	{
		$this->links = $links;
	}

	public function getLinkByRel($rel)
	{
		foreach($this->links as $link)
		{
			if($link->getRel() == $rel)
			{
				return $link;
			}
		}

		return null;
	}
}
