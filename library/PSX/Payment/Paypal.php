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

namespace PSX\Payment;

use PSX\Data\Importer;
use PSX\Data\Reader;
use PSX\Data\Record\StoreInterface;
use PSX\Data\Writer;
use PSX\Payment\Paypal\Data;
use PSX\Payment\Paypal\Credentials;
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
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Paypal
{
	const PAYMENT = '/v1/payments/payment';
	const SALE    = '/v1/payments/sale';
	const REFUND  = '/v1/payments/refund';
	const CREDIT  = '/v1/vault/credit-card';
	const TOKEN   = '/v1/oauth2/token';

	protected $http;
	protected $credentials;
	protected $importer;
	protected $oauth2;
	protected $store;

	protected $accessToken;
	protected $approvalUrl;

	public function __construct(Http $http, Credentials $credentials, Importer $importer, StoreInterface $store = null)
	{
		if($http->getHandler() instanceof Http\Handler\Curl)
		{
			$caInfo = realpath($credentials->getCertificate());

			$http->getHandler()->setCaInfo($caInfo);
		}

		$this->http        = $http;
		$this->credentials = $credentials;
		$this->importer    = $importer;
		$this->oauth2      = new Oauth2();
		$this->store       = $store;
	}

	public function getAccessToken()
	{
		if($this->accessToken === null)
		{
			$accessToken = null;

			if($this->store !== null)
			{
				$accessToken = $this->store->load(__CLASS__);
			}

			if(!$accessToken instanceof AccessToken)
			{
				$cred = new ClientCredentials($this->http, new Url($this->credentials->getEndpoint() . self::TOKEN), $this->importer);
				$cred->setClientPassword($this->credentials->getClientId(), $this->credentials->getClientSecret());

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

			$this->accessToken = $accessToken;
		}

		return $this->accessToken;
	}

	public function createPaymentBuilder()
	{
		return new PaymentBuilder();
	}

	public function createPayment(Data\Payment $payment)
	{
		$writer   = new Writer\Json();
		$body     = $writer->write($payment);
		$header   = array(
			'Authorization' => $this->oauth2->getAuthorizationHeader($this->getAccessToken()),
			'Content-Type'  => 'application/json',
		);

		$request  = new PostRequest(new Url($this->credentials->getEndpoint() . self::PAYMENT), $header, $body);
		$response = $this->http->request($request);

		if($response->getStatusCode() == 201)
		{
			$payment = $this->importer->import(new Data\Payment(), $response);

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

		$request  = new GetRequest(new Url($this->credentials->getEndpoint() . self::PAYMENT . '/' . $paymentId), $header);
		$response = $this->http->request($request);

		if($response->getStatusCode() == 200)
		{
			return $this->importer->import(new Data\Payment(), $response);
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

		$request  = new PostRequest(new Url($this->credentials->getEndpoint() . self::PAYMENT . '/' . $paymentId . '/execute/'), $header, $body);
		$response = $this->http->request($request);

		if($response->getStatusCode() == 200)
		{
			return $this->importer->import(new Data\Payment(), $response);
		}
		else
		{
			$error = Json::decode($response->getBody());

			$this->handleError($error);
		}
	}

	public function getPayments($count = null, $startIndex = null, $sortBy = null, $sortOrder = null, $startId = null, DateTime $startTime = null, DateTime $endTime = null)
	{
		$url    = new Url($this->credentials->getEndpoint() . self::PAYMENT);
		$header = array(
			'Authorization' => $this->oauth2->getAuthorizationHeader($this->getAccessToken()),
			'Content-Type'  => 'application/json',
		);

		if($count !== null && $count > 0 && $count <= 20)
		{
			$url->addParam('count', (int) $count);
		}

		if($startIndex !== null && $startIndex > 0)
		{
			$url->addParam('start_index', (int) $startIndex);
		}

		if($sortBy !== null && in_array($sortBy, array('create_time', 'update_time')))
		{
			$url->addParam('sort_by', $sortBy);
		}

		if($sortOrder !== null && in_array($sortBy, array('asc', 'desc')))
		{
			$url->addParam('sort_order', $sortOrder);
		}

		if($startId !== null)
		{
			$url->addParam('start_id', $startId);
		}

		if($startTime !== null)
		{
			$url->addParam('start_time', $startTime->format(DateTime::RFC3339));
		}

		if($endTime !== null)
		{
			$url->addParam('end_time', $endTime->format(DateTime::RFC3339));
		}

		$request  = new GetRequest($url, $header);
		$response = $this->http->request($request);

		if($response->getStatusCode() == 200)
		{
			return $this->importer->import(new Data\Payments(), $response);
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

		throw new Exception('Unknown error occured');
	}
}
