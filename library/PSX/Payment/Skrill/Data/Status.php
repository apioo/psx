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

namespace PSX\Payment\Skrill\Data;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Status
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Status extends RecordAbstract
{
	protected $payToEmail;
	protected $payFromEmail;
	protected $merchantId;
	protected $customerId;
	protected $transactionId;
	protected $mbTransactionId;
	protected $mbAmount;
	protected $mbCurrency;
	protected $status;
	protected $failedReasonCode;
	protected $md5sig;
	protected $sha2sig;
	protected $amount;
	protected $currency;
	protected $paymentType;

	public function getRecordInfo()
	{
		return new RecordInfo('status', array(
			'pay_to_email'       => $this->payToEmail,
			'pay_from_email'     => $this->payFromEmail,
			'merchant_id'        => $this->merchantId,
			'customer_id'        => $this->customerId,
			'transaction_id'     => $this->transactionId,
			'mb_transaction_id'  => $this->mbTransactionId,
			'mb_amount'          => $this->mbAmount,
			'mb_currency'        => $this->mbCurrency,
			'status'             => $this->status,
			'failed_reason_code' => $this->failedReasonCode,
			'md5sig'             => $this->md5sig,
			'sha2sig'            => $this->sha2sig,
			'amount'             => $this->amount,
			'currency'           => $this->currency,
			'payment_type'       => $this->paymentType,
		));
	}

	public function setPayToEmail($payToEmail)
	{
		$this->payToEmail = $payToEmail;
	}
	
	public function getPayToEmail()
	{
		return $this->payToEmail;
	}

	public function setPayFromEmail($payFromEmail)
	{
		$this->payFromEmail = $payFromEmail;
	}
	
	public function getPayFromEmail()
	{
		return $this->payFromEmail;
	}

	public function setMerchantId($merchantId)
	{
		$this->merchantId = $merchantId;
	}
	
	public function getMerchantId()
	{
		return $this->merchantId;
	}

	public function setCustomerId($customerId)
	{
		$this->customerId = $customerId;
	}
	
	public function getCustomerId()
	{
		return $this->customerId;
	}

	public function setTransactionId($transactionId)
	{
		$this->transactionId = $transactionId;
	}
	
	public function getTransactionId()
	{
		return $this->transactionId;
	}

	public function setMbTransactionId($mbTransactionId)
	{
		$this->mbTransactionId = $mbTransactionId;
	}
	
	public function getMbTransactionId()
	{
		return $this->mbTransactionId;
	}

	public function setMbAmount($mbAmount)
	{
		$this->mbAmount = $mbAmount;
	}
	
	public function getMbAmount()
	{
		return $this->mbAmount;
	}

	public function setMbCurrency($mbCurrency)
	{
		$this->mbCurrency = $mbCurrency;
	}
	
	public function getMbCurrency()
	{
		return $this->mbCurrency;
	}

	public function setStatus($status)
	{
		$this->status = $status;
	}
	
	public function getStatus()
	{
		return $this->status;
	}

	public function setFailedReasonCode($failedReasonCode)
	{
		$this->failedReasonCode = $failedReasonCode;
	}
	
	public function getFailedReasonCode()
	{
		return $this->failedReasonCode;
	}

	public function setMd5sig($md5sig)
	{
		$this->md5sig = $md5sig;
	}
	
	public function getMd5sig()
	{
		return $this->md5sig;
	}

	public function setSha2sig($sha2sig)
	{
		$this->sha2sig = $sha2sig;
	}
	
	public function getSha2sig()
	{
		return $this->sha2sig;
	}

	public function setAmount($amount)
	{
		$this->amount = $amount;
	}
	
	public function getAmount()
	{
		return $this->amount;
	}

	public function setCurrency($currency)
	{
		$this->currency = $currency;
	}
	
	public function getCurrency()
	{
		return $this->currency;
	}

	public function setPaymentType($paymentType)
	{
		$this->paymentType = $paymentType;
	}
	
	public function getPaymentType()
	{
		return $this->paymentType;
	}

	/**
	 * Returns true if the provided signature is correct else false
	 *
	 * @return boolean
	 */
	public function verifySignature()
	{
		$secret = strtoupper(md5(Skrill::SECRET_WORD));
		$ownSig = strtoupper(md5($this->merchantId . $this->transactionId . $secret . $this->mbAmount . $this->mbCurrency . $this->status));

		return strcmp($this->md5sig, $ownSig) === 0;
	}
}

