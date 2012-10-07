<?php
/*
 *  $Id: ExpressCheckout.php 515 2012-06-11 21:35:56Z k42b3.x@googlemail.com $
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
 * PSX_Payment_Paypal_ExpressCheckout
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Payment
 * @version    $Revision: 515 $
 */
class PSX_Payment_Paypal_ExpressCheckout
{
	const API  = 'https://api.sandbox.paypal.com/nvp';
	const WEB  = 'https://www.sandbox.paypal.com/webscr';
	const VER  = '89.0';

	const USER = 'sdk-three_api1.sdk.com';
	const PWD  = 'QFZCWN5HZM8VBG7Q';
	const SIG  = 'A-IzJhZZjhg29XQ2qnhapuwxIDzyAZQ92FRP5dqBzVesOkzbdUONzmOU';

	public static $currencyCodes = array('AUD', 'BRL', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'NOK', 'NZD', 'PHP', 'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'TWD', 'THB', 'TRY', 'USD');

	private $http;
	private $store;
	private $currencyId;

	public function __construct(PSX_Http $http, PSX_Payment_Paypal_StoreInterface $store = null)
	{
		$this->http  = $http;
		$this->store = $store === null ? new PSX_Payment_Paypal_Store_Session() : $store;

		// set default currency
		$this->setCurrencyId('EUR');
	}

	public function setStore(PSX_Payment_Paypal_StoreInterface $store)
	{
		$this->store = $store;
	}

	/**
	 * Starts an express checkout and returns the result as array
	 *
	 * @see https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_SetExpressCheckout
	 * @return array
	 */
	public function setExpressCheckout($amount, $returnUrl, $cancelUrl)
	{
		// build request data
		$data = array(

			'METHOD'    => 'SetExpressCheckout',
			'VERSION'   => self::VER,
			'USER'      => self::USER,
			'PWD'       => self::PWD,
			'SIGNATURE' => self::SIG,
			'RETURNURL' => $returnUrl,
			'CANCELURL' => $cancelUrl,

		);

		if(is_array($amount))
		{
			$count = count($amount);

			foreach($amount as $k => $value)
			{
				$data['PAYMENTREQUEST_' . $k . '_AMT']           = $amount;
				$data['PAYMENTREQUEST_' . $k . '_CURRENCYCODE']  = $this->currencyId;
				$data['PAYMENTREQUEST_' . $k . '_PAYMENTACTION'] = 'Sale';
			}
		}
		else
		{
			$count = 1;

			$data['PAYMENTREQUEST_0_AMT']           = $amount;
			$data['PAYMENTREQUEST_0_CURRENCYCODE']  = $this->currencyId;
			$data['PAYMENTREQUEST_0_PAYMENTACTION'] = 'Sale';
		}

		// request
		$url      = new PSX_Url(self::API);
		$request  = new PSX_Http_PostRequest($url, array(), $data);
		$response = $this->http->request($request);

		if($response->getCode() == 200)
		{
			$resp = array();

			parse_str($response->getBody(), $resp);

			self::assertResponse($resp, $count);

			if(isset($resp['TOKEN']))
			{
				// save token
				$this->store->saveToken(self::getSessionId(), $resp['TOKEN']);

				return $resp;
			}
			else
			{
				throw new PSX_Payment_Paypal_Exception('No token set');
			}
		}
		else
		{
			throw new PSX_Payment_Paypal_Exception('Invalid response code');
		}
	}

	/**
	 * Redirects the user to the paypal endpoint using the obtained token.
	 * Throws an exception if no token was set
	 *
	 * @return void
	 */
	public function redirect()
	{
		$token = $this->store->loadToken(self::getSessionId());

		if(empty($token))
		{
			throw new PSX_Payment_Paypal_Exception('No token available');
		}

		$url = new PSX_Url(self::WEB);
		$url->addParam('cmd', '_express-checkout');
		$url->addParam('token', $token);

		header('Location: ' . strval($url));
		exit;
	}

	/**
	 * Returns an array containing informations about the transaction wich was
	 * previously started with setExpressCheckout. Throws an exception if no
	 * token was set
	 *
	 * @see https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_GetExpressCheckoutDetails
	 * @return array
	 */
	public function getExpressCheckoutDetails()
	{
		// get token
		$token = $this->store->loadToken(self::getSessionId());

		if(empty($token))
		{
			throw new PSX_Payment_Paypal_Exception('No token available');
		}

		// build request data
		$data = array(

			'METHOD'    => 'GetExpressCheckoutDetails',
			'VERSION'   => self::VER,
			'USER'      => self::USER,
			'PWD'       => self::PWD,
			'SIGNATURE' => self::SIG,
			'TOKEN'     => $token,

		);

		// request
		$url      = new PSX_Url(self::API);
		$request  = new PSX_Http_PostRequest($url, array(), $data);
		$response = $this->http->request($request);

		if($response->getCode() == 200)
		{
			$resp = array();

			parse_str($response->getBody(), $resp);

			self::assertResponse($resp);

			if(isset($resp['PAYERID']))
			{
				// save payer id
				$this->store->savePayerId(self::getSessionId(), $resp['PAYERID']);
			}

			return $resp;
		}
		else
		{
			throw new PSX_Payment_Paypal_Exception('Invalid response code');
		}
	}

	/**
	 * Finishes the transaction and returns the result as array
	 *
	 * @see https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_DoExpressCheckoutPayment
	 * @return array
	 */
	public function doExpressCheckoutPayment($amount)
	{
		// get token
		$token = $this->store->loadToken(self::getSessionId());

		if(empty($token))
		{
			throw new PSX_Payment_Paypal_Exception('No token available');
		}

		// get payerid
		$payerId = $this->store->loadPayerId(self::getSessionId());

		if(empty($payerId))
		{
			throw new PSX_Payment_Paypal_Exception('No payer id available');
		}

		// build request data
		$data = array(

			'METHOD'    => 'DoExpressCheckoutPayment',
			'VERSION'   => self::VER,
			'USER'      => self::USER,
			'PWD'       => self::PWD,
			'SIGNATURE' => self::SIG,
			'TOKEN'     => $token,
			'PAYERID'   => $payerId,

		);

		if(is_array($amount))
		{
			$count = count($amount);

			foreach($amount as $k => $value)
			{
				$data['PAYMENTREQUEST_' . $k . '_AMT']           = $amount;
				$data['PAYMENTREQUEST_' . $k . '_CURRENCYCODE']  = $this->currencyId;
				$data['PAYMENTREQUEST_' . $k . '_PAYMENTACTION'] = 'Sale';
			}
		}
		else
		{
			$count = 1;

			$data['PAYMENTREQUEST_0_AMT']           = $amount;
			$data['PAYMENTREQUEST_0_CURRENCYCODE']  = $this->currencyId;
			$data['PAYMENTREQUEST_0_PAYMENTACTION'] = 'Sale';
		}

		// request
		$url      = new PSX_Url(self::API);
		$request  = new PSX_Http_PostRequest($url, array(), $data);
		$response = $this->http->request($request);

		if($response->getCode() == 200)
		{
			$resp = array();

			parse_str($response->getBody(), $resp);

			self::assertResponse($resp, $count);

			return $resp;
		}
		else
		{
			throw new PSX_Payment_Paypal_Exception('Invalid response code');
		}
	}

	public function setCurrencyId($currencyId)
	{
		if(in_array($currencyId, self::$currencyCodes))
		{
			$this->currencyId = $currencyId;
		}
		else
		{
			throw new PSX_Payment_Paypal_Exception('Invalid currency');
		}
	}

	public static function getSessionId()
	{
		return session_id();
	}

	public static function assertResponse(array $data, $count = 1)
	{
		if(isset($data['ACK']))
		{
			switch($data['ACK'])
			{
				case 'Success':
				case 'SuccessWithWarning':

					return true;
					break;

				case 'Failure':
				case 'FailureWithWarning':

					for($i = 0; $i < $count; $i++)
					{
						if(isset($data['L_ERRORCODE' . $i]) && isset($data['L_LONGMESSAGE' . $i]))
						{
							throw new PSX_Payment_Paypal_Exception($data['L_LONGMESSAGE' . $i], $data['L_ERRORCODE' . $i]);
						}
					}

					throw new PSX_Payment_Paypal_Exception('Error occured');
					break;
			}
		}
		else
		{
			throw new PSX_Payment_Paypal_Exception('ACK key not set');
		}
	}
}

