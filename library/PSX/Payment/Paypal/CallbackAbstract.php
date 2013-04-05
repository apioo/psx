<?php
/*
 *  $Id: StoreInterface.php 542 2012-07-10 20:20:59Z k42b3.x@googlemail.com $
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

namespace PSX\Payment\Paypal;

use PSX\Payment\Paypal;
use PSX\Payment\Paypal\Data\Payment;
use PSX\ModuleAbstract;
use PSX\Http;
use PSX\Exception;

/**
 * StoreInterface
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Payment
 * @version    $Revision: 542 $
 */
abstract class CallbackAbstract extends ModuleAbstract
{
	protected $paypal;

	/**
	 * @httpMethod GET
	 * @path /return
	 */
	public function doReturn()
	{
		$paypal    = $this->getPaypal();
		$paymentId = $this->getPaymentId();

		if(!empty($paymentId))
		{
			$token   = $this->parameter->token('string');
			$payerId = $this->parameter->PayerID('string');
			$payment = $paypal->executePayment($paymentId, $payerId);

			if($payment instanceof Payment)
			{
				$this->onPayment($payment);
			}
			else
			{
				throw new Exception('Invalid response');
			}
		}
		else
		{
			throw new Exception('Payment id not available');
		}
	}

	/**
	 * @httpMethod GET
	 * @path /cancel
	 */
	public function doCancel()
	{
		$paypal    = $this->getPaypal();
		$paymentId = $this->getPaymentId();

		if(!empty($paymentId))
		{
			$token   = $this->parameter->token('string');
			$payment = $paypal->getPayment($paymentId);

			if($payment instanceof Payment)
			{
				$this->onCancel($payment);
			}
			else
			{
				throw new Exception('Invalid response');
			}
		}
		else
		{
			throw new Exception('Payment id not available');
		}
	}

	protected function getPaypal()
	{
		if($this->paypal == null)
		{
			$http    = new Http();
			$session = new Paypal\Store\Session();
			$paypal  = new Paypal($http, $session);

			$this->paypal = $paypal;
		}

		return $this->paypal;
	}

	/**
	 * Returns the payment id of the payment created for this session
	 *
	 * @return string
	 */
	abstract public function getPaymentId();

	/**
	 * Is called if the return endpoint was requested. Contains the executed 
	 * payment. You have to check whether the payment was approved
	 *
	 * @param PSX\Payment\Paypal\Data\Payment
	 * @return void
	 */
	abstract public function onPayment(Payment $payment);

	/**
	 * Is called of the cancel endpoint was requested. Contains alll available
	 * informations of the payment
	 *
	 * @param PSX\Payment\Paypal\Data\Payment
	 * @return void
	 */
	abstract public function onCancel(Payment $payment);
}
