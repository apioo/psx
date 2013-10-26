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

namespace PSX\Dispatch\RequestFilter;

use Closure;
use PSX\Base;
use PSX\Dispatch\RequestFilterInterface;
use PSX\Exception;
use PSX\Http\Request;
use PSX\Http\Authentication;
use PSX\Oauth;
use PSX\Oauth\Provider\Data\Consumer;

/**
 * OauthAuthentication
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class OauthAuthentication implements RequestFilterInterface
{
	protected $consumerCallback;
	protected $successCallback;
	protected $failureCallback;
	protected $missingCallback;

	/**
	 * The consumerCallback is called with the given consumerKey and token. The
	 * callback should return an PSX\Oauth\Provider\Data\Consumer. If the 
	 * signature is valid the onSuccess else the onFailure callback is called.
	 * If the Authorization header is missing the onMissing callback is called
	 *
	 * @param Closure $consumerCallback
	 */
	public function __construct(Closure $consumerCallback)
	{
		$this->consumerCallback = $consumerCallback;

		$this->onSuccess(function(){
			// authentication successful
		});

		$this->onFailure(function(){
			throw new Exception('Invalid consumer key or signature');
		});

		$this->onMissing(function(){
			$params = array(
				'realm' => 'psx',
			);

			Base::setResponseCode(401);
			header('WWW-Authenticate: Oauth ' . Authentication::encodeParameters($params), false);

			throw new Exception('Missing authorization header');
		});
	}

	public function handle(Request $request)
	{
		$authorization = $request->getHeader('Authorization');

		if(!empty($authorization))
		{
			$parts = explode(' ', $authorization, 2);
			$type  = isset($parts[0]) ? $parts[0] : null;
			$data  = isset($parts[1]) ? $parts[1] : null;

			if($type == 'OAuth' && !empty($data))
			{
				$params = Authentication::decodeParameters($data);
				$params = array_map(array('\PSX\Oauth', 'urlDecode'), $params);

				// realm is not used in the base string
				unset($params['realm']);

				if(!isset($params['oauth_consumer_key']))
				{
					throw new Exception('Consumer key not set');
				}

				if(!isset($params['oauth_token']))
				{
					throw new Exception('Token not set');
				}

				if(!isset($params['oauth_signature_method']))
				{
					throw new Exception('Signature method not set');
				}

				if(!isset($params['oauth_signature']))
				{
					throw new Exception('Signature not set');
				}

				$consumer = call_user_func_array($this->consumerCallback, array($params['oauth_consumer_key'], $params['oauth_token']));

				if($consumer instanceof Consumer)
				{
					$signature = Oauth::getSignature($params['oauth_signature_method']);

					$method = $_SERVER['REQUEST_METHOD'];
					$url    = $request->getUrl();
					$params = array_merge($params, $_GET);

					if(strpos($request->getHeader('Content-Type'), 'application/x-www-form-urlencoded') !== false)
					{
						$params = array_merge($params, $_POST);
					}

					$baseString = Oauth::buildBasestring($method, $url, $params);

					if($signature->verify($baseString, $consumer->getConsumerSecret(), $consumer->getTokenSecret(), $params['oauth_signature']) !== false)
					{
						$this->callSuccess();
					}
					else
					{
						$this->callFailure();
					}
				}
				else
				{
					$this->callFailure();
				}
			}
			else
			{
				$this->callMissing();
			}
		}
		else
		{
			$this->callMissing();
		}
	}

	public function onSuccess(Closure $successCallback)
	{
		$this->successCallback = $successCallback;
	}

	public function onFailure(Closure $failureCallback)
	{
		$this->failureCallback = $failureCallback;
	}

	public function onMissing(Closure $missingCallback)
	{
		$this->missingCallback = $missingCallback;
	}

	protected function callSuccess()
	{
		call_user_func($this->successCallback);
	}

	protected function callFailure()
	{
		call_user_func($this->failureCallback);
	}

	protected function callMissing()
	{
		call_user_func($this->missingCallback);
	}
}
