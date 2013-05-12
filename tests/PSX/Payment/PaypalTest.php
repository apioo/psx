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

namespace PSX\Payment;

use PSX\Http;
use PSX\Payment\Paypal;
use PSX\Payment\Paypal\Data;
use PSX\Url;

/**
 * PaypalTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class PaypalTest extends \PHPUnit_Framework_TestCase
{
	private $http;
	private $paypal;

	protected function setUp()
	{
		$this->http   = new Http();
		$this->paypal = new Paypal($this->http);
	}

	public function testCreatePayment()
	{
		// create payment
		$payer = new Data\Payer();
		$payer->setPaymentMethod('paypal');

		$amount = new Data\Amount();
		$amount->setCurrency('USD');
		$amount->setTotal('13.37');

		$transaction = new Data\Transaction();
		$transaction->setAmount($amount);
		$transaction->setDescription('Test payment transaction');

		$redirectUrls = new Data\RedirectUrls();
		$redirectUrls->setReturnUrl(new Url('http://127.0.0.1/return'));
		$redirectUrls->setCancelUrl(new Url('http://127.0.0.1/cancel'));

		$payment = new Data\Payment();
		$payment->setIntent('sale');
		$payment->setPayer($payer);
		$payment->setRedirectUrls($redirectUrls);
		$payment->addTransaction($transaction);

		$payment = $this->paypal->createPayment($payment);

		$this->assertEquals('created', $payment->getState());
	}
}
