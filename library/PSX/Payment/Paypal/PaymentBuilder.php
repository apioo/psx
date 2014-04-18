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

namespace PSX\Payment\Paypal;

use PSX\Url;

/**
 * PaymentBuilder
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class PaymentBuilder
{
	protected $payment;

	public function __construct()
	{
		$this->payment = new Data\Payment();
		$this->payment->setIntent('sale');
	}

	public function setPayer($paymentMethod, Data\FundingInstrument $fundingInstrument = null)
	{
		$payer = new Data\Payer();
		$payer->setPaymentMethod($paymentMethod);

		if($fundingInstrument !== null)
		{
			$payer->setFundingInstrument($fundingInstrument);
		}

		$this->payment->setPayer($payer);

		return $this;
	}

	public function addTransaction($price, $currency, $description)
	{
		$amount = new Data\Amount();
		$amount->setCurrency($currency);
		$amount->setTotal($price);

		$transaction = new Data\Transaction();
		$transaction->setAmount($amount);
		$transaction->setDescription($description);

		$this->payment->addTransaction($transaction);

		return $this;
	}

	public function setRedirectUrls(Url $callbackUrl, Url $cancelUrl)
	{
		$redirectUrls = new Data\RedirectUrls();
		$redirectUrls->setReturnUrl($callbackUrl);
		$redirectUrls->setCancelUrl($callbackUrl);

		$this->payment->setRedirectUrls($redirectUrls);

		return $this;
	}

	public function getPayment()
	{
		return $this->payment;
	}
}
