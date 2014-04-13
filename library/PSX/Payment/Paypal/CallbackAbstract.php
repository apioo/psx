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

use PSX\Payment\Paypal;
use PSX\Payment\Paypal\Data\Payment;
use PSX\ControllerAbstract;
use PSX\Http;
use PSX\Exception;
use PSX\Data\RecordStore;

/**
 * CallbackAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class CallbackAbstract extends ControllerAbstract
{
	protected $paypal;

	public function onGet()
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
				if($payment->getState() == 'approved')
				{
					$this->onPayment($payment);
				}
				else
				{
					$this->onCancel($payment);
				}
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
			$session = new RecordStore\Session();
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
	 * payment
	 *
	 * @param PSX\Payment\Paypal\Data\Payment
	 * @return void
	 */
	abstract public function onPayment(Payment $payment);

	/**
	 * Is called if the cancel endpoint was requested or the payment was not 
	 * approved
	 *
	 * @param PSX\Payment\Paypal\Data\Payment
	 * @return void
	 */
	abstract public function onCancel(Payment $payment);
}
