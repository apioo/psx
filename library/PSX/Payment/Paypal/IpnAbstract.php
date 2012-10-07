<?php
/*
 *  $Id: IpnAbstract.php 496 2012-06-02 18:41:54Z k42b3.x@googlemail.com $
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

/**
 * PSX_Payment_Paypal_IpnAbstract
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Payment
 * @version    $Revision: 496 $
 */
abstract class PSX_Payment_Paypal_IpnAbstract extends PSX_Module_ApiAbstract
{
	//const ENDPOINT = 'https://www.paypal.com/cgi-bin/webscr';
	const ENDPOINT = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

	protected function handle()
	{
		switch(PSX_Base::getRequestMethod())
		{
			case 'POST':

				$this->processIpnMessage();

				exit;

				break;

			default:

				throw new PSX_Payment_Paypal_Exception('Method not allowed', 405);

				break;
		}
	}

	protected function processIpnMessage()
	{
		$data    = 'cmd=_notify-validate';
		$request = PSX_Base::getRawInput();

		if(!empty($request))
		{
			$data    = $data . '&' . $request;
			$http    = new PSX_Http(new PSX_Http_Handler_Curl());
			$request = new PSX_Http_PostRequest(self::ENDPOINT, array(), $data);

			$response = $http->send($request);

			if($response->getCode() == 200)
			{
				$message = new PSX_Payment_Paypal_Ipn_Message();
				$message->import($this->getRequest(PSX_Data_ReaderInterface::FORM));

				if(strpos($response->getBody(), 'VERIFIED') !== false)
				{
					$this->onVerified($message);
				}
				else if(strpos($response->getBody(), 'INVALID') !== false)
				{
					$this->onInvalid($message);
				}
				else
				{
					throw new PSX_Payment_Paypal_Exception('Invalid response format');
				}
			}
			else
			{
				throw new PSX_Payment_Paypal_Exception('Invalid response code');
			}
		}
		else
		{
			throw new PSX_Payment_Paypal_Exception('No post data given');
		}
	}

	/**
	 * Is called if an IPN mesage was successful verified by paypal. After you
	 * receive the VERIFIED message, there are several important checks you must
	 * perform before you can assume that the message is legitimate and not
	 * already processed:
	 * 	- Confirm that the payment status is Completed.
	 * 	- Use the transaction ID to verify that the transaction has not already
	 * 	  been processed, which prevents duplicate transactions from being
	 * 	  processed.
	 * 	- Validate that the receivers email address is registered to you.
	 * 	- Verify that the price, item description, and so on, match the
	 * 	  transaction on your website.
	 *
	 * @param PSX_Payment_Paypal_Ipn_Message $message
	 * @return void
	 */
	abstract protected function onVerified(PSX_Payment_Paypal_Ipn_Message $message);

	/**
	 * Is called if an IPN message was invalid
	 *
	 * @param PSX_Payment_Paypal_Ipn_Message $message
	 * @return void
	 */
	abstract protected function onInvalid(PSX_Payment_Paypal_Ipn_Message $message);
}

