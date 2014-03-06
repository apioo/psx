<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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
 * Payer
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Payer extends RecordAbstract
{
	protected $paymentMethod;
	protected $fundingInstruments;
	protected $payerInfo;

	public function getRecordInfo()
	{
		return new RecordInfo('payer', array(
			'payment_method'      => $this->paymentMethod,
			'funding_instruments' => $this->fundingInstruments,
			'payer_info'          => $this->payerInfo,
		));
	}

	public function getPaymentMethod()
	{
		return $this->paymentMethod;
	}

	public function setPaymentMethod($paymentMethod)
	{
		if(!in_array($paymentMethod, array('credit_card', 'paypal')))
		{
			throw new Exception('Invalid payment method');
		}

		$this->paymentMethod = $paymentMethod;
	}

	public function getFundingInstruments()
	{
		return $this->fundingInstruments;
	}

	/**
	 * @param array<PSX\Payment\Paypal\Data\FundingInstrument> $fundingInstruments
	 */
	public function setFundingInstruments($fundingInstruments)
	{
		$this->fundingInstruments = $fundingInstruments;
	}

	public function addFundingInstrument(FundingInstrument $fundingInstrument)
	{
		$this->fundingInstruments[] = $fundingInstrument;
	}

	public function getPayerInfo()
	{
		return $this->payerInfo;
	}

	/**
	 * @param PSX\Payment\Paypal\Data\PayerInfo $payerInfo
	 */
	public function setPayerInfo(PayerInfo $payerInfo)
	{
		$this->payerInfo = $payerInfo;
	}
}
