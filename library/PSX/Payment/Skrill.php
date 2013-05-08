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

use PSX\Payment\Skrill\Data;
use PSX\Exception;
use PSX\Http;
use PSX\Http\Cookie;
use PSX\Http\PostRequest;
use PSX\Url;

/**
 * Skrill
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Skrill
{
	const STATUS_PROCESSED  = 2;
	const STATUS_PENDING    = 0;
	const STATUS_CANCELLED  = -1;
	const STATUS_FAILED     = -2;
	const STATUS_CHARGEBACK = -3;

	const SECRET_WORD = '';

	//const ENDPOINT    = 'https://www.moneybookers.com/app/payment.pl';
	const ENDPOINT    = 'https://www.moneybookers.com/app/test_payment.pl';
	const CERTIFICATE = 'www.moneybookers.com.pem';

	protected $http;
	protected $sessionId;

	public function __construct(Http $http)
	{
		if($http->getHandler() instanceof Http\Handler\Curl)
		{
			$caInfo = realpath(__DIR__ . '/Skrill/' . self::CERTIFICATE);
			//$http->getHandler()->setCaInfo($caInfo);
		}

		$this->http = $http;
	}

	public function createPayment(Data\Payment $payment)
	{
		$payment->setPrepareOnly(1);

		// request
		$body     = http_build_query($payment->getData(), '', '&');
		$header   = array(
			'Content-Type' => 'application/x-www-form-urlencoded'
		);
		$request  = new PostRequest(new Url(self::ENDPOINT), $header, $body);
		$response = $this->http->request($request);

		if($response->getCode() == 200)
		{
			$cookies   = $response->getHeader('Set-Cookie');
			$sessionId = null;

			if(is_array($cookies))
			{
				foreach($cookies as $cookie)
				{
					$cookie = Cookie::convert($cookie);

					if($cookie->getName() == 'SESSION_ID')
					{
						$sessionId = $cookie->getValue();
						break;
					}
				}
			}

			if(!empty($sessionId))
			{
				$this->sessionId = $sessionId;

				return true;
			}
			else
			{
				throw new Exception('Could not find session id');
			}
		}
		else
		{
			throw new Exception('Invalid response');
		}
	}

	public function redirect()
	{
		if(empty($this->sessionId))
		{
			throw new Exception('No session id available');
		}

		header('Location: ' . self::ENDPOINT . '?sid=' . $this->sessionId);
		exit;
	}
}
