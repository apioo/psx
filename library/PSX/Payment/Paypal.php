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

namespace PSX\Payment;

use PSX\Data\Reader;
use PSX\Data\RecordStoreInterface;
use PSX\Payment\Paypal\Data;
use PSX\Oauth2;
use PSX\Oauth2\Authorization\ClientCredentials;
use PSX\Oauth2\AccessToken;
use PSX\Http;
use PSX\Http\GetRequest;
use PSX\Http\PostRequest;
use PSX\Json;
use PSX\Url;
use PSX\Exception;

/**
 * Payment
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Payment
 * @version    $Revision: 496 $
 */
class Paypal
{
	//const ENDPOINT      = 'https://api.paypal.com';
	const ENDPOINT      = 'https://api.sandbox.paypal.com';
	const CLIENT_ID     = 'AbGnaxBKBZGDeEYdiF9K5S4PgCydA6vp_7F24PQOVNSDRv8PZ8XiCPXkS4HV';
	const CLIENT_SECRET = 'EEjJJBCD6AIExdxj9a5_1hY8IpH-WpwuUHsp0HTWzrFQC66ycYuiPEwG6wr4';
	//const CERTIFICATE   = 'api.paypal.com.pem';
	const CERTIFICATE   = 'api.sandbox.paypal.com.pem';

	const PAYMENT = '/v1/payments/payment';
	const SALE    = '/v1/payments/sale';
	const REFUND  = '/v1/payments/refund';
	const CREDIT  = '/v1/vault/credit-card';
	const TOKEN   = '/v1/oauth2/token';

	protected $http;
	protected $oauth2;
	protected $store;
	protected $accessToken;

	protected $approvalUrl;

	public function __construct(Http $http, RecordStoreInterface $store = null)
	{
		if($http->getHandler() instanceof Http\Handler\Curl)
		{
			$caInfo = realpath(__DIR__ . '/Paypal/' . self::CERTIFICATE);
			$http->getHandler()->setCaInfo($caInfo);
		}

		$this->http   = $http;
		$this->oauth2 = new Oauth2();
		$this->store  = $store;
	}

	public function getAccessToken()
	{
		$accessToken = null;

		if($this->store !== null)
		{
			$accessToken = $this->store->load(__CLASS__);
		}

		if(!$accessToken instanceof AccessToken)
		{
			$cred = new ClientCredentials($this->http, new Url(self::ENDPOINT . self::TOKEN));
			$cred->setClientPassword(self::CLIENT_ID, self::CLIENT_SECRET);

			$accessToken = $cred->getAccessToken();

			if($accessToken instanceof AccessToken)
			{
				if($this->store !== null)
				{
					$this->store->save(__CLASS__, $accessToken);
				}
			}
			else
			{
				throw new Exception('Could not get access token');
			}
		}

		return $accessToken;
	}

	public function createPayment(Data\Payment $payment)
	{
		$body     = Json::encode($payment->getData());
		$header   = array(
			'Authorization' => $this->oauth2->getAuthorizationHeader($this->getAccessToken()),
			'Content-Type'  => 'application/json',
		);

		$request  = new PostRequest(new Url(self::ENDPOINT . self::PAYMENT), $header, $body);
		$response = $this->http->request($request);

		if($response->getCode() == 201)
		{
			$reader  = new Reader\Json();
			$result  = $reader->read($response);
			$payment = new Data\Payment();
			$payment->import($result);

			// save approval uri
			$link = $payment->getLinkByRel('approval_url');

			if($link instanceof Data\Link)
			{
				$this->approvalUrl = $link->getHref();
			}

			return $payment;
		}
		else
		{
			$error = Json::decode($response->getBody());

			$this->handleError($error);
		}
	}

	public function redirect()
	{
		if(empty($this->approvalUrl))
		{
			throw new Exception('No approval url available');
		}

		header('Location: ' . $this->approvalUrl);
		exit;
	}

	public function getPayment($paymentId)
	{
		$header   = array(
			'Authorization' => $this->oauth2->getAuthorizationHeader($this->getAccessToken()),
			'Content-Type'  => 'application/json',
		);

		$request  = new GetRequest(new Url(self::ENDPOINT . self::PAYMENT . '/' . $paymentId), $header);
		$response = $this->http->request($request);

		if($response->getCode() == 200)
		{
			$reader  = new Reader\Json();
			$result  = $reader->read($response);
			$payment = new Data\Payment();
			$payment->import($result);

			return $payment;
		}
		else
		{
			$error = Json::decode($response->getBody());

			$this->handleError($error);
		}
	}

	public function executePayment($paymentId, $payerId)
	{
		$body     = Json::encode(array('payer_id' => $payerId));
		$header   = array(
			'Authorization' => $this->oauth2->getAuthorizationHeader($this->getAccessToken()),
			'Content-Type'  => 'application/json',
		);

		$request  = new PostRequest(new Url(self::ENDPOINT . self::PAYMENT . '/' . $paymentId . '/execute/'), $header, $body);
		$response = $this->http->request($request);

		if($response->getCode() == 200)
		{
			$reader  = new Reader\Json();
			$result  = $reader->read($response);
			$payment = new Data\Payment();
			$payment->import($result);

			return $payment;
		}
		else
		{
			$error = Json::decode($response->getBody());

			$this->handleError($error);
		}
	}

	protected function handleError($data)
	{
		if(isset($data['name']))
		{
			$className = 'PSX\Payment\Paypal\Exception\\' . implode('', array_map('ucfirst', explode('_', strtolower($data['name'])))) . 'Exception';
			$message   = isset($data['message']) ? $data['message'] : 'No message available';

			if(class_exists($className))
			{
				throw new $className($message);
			}
		}

		session_destroy();
		var_dump($_SESSION, $this->http->getResponse());exit;

		throw new Exception('Unknown error occured');
	}
}
