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

namespace PSX\Dispatch\Filter;

use Closure;
use Psr\HttpMessage\RequestInterface;
use Psr\HttpMessage\ResponseInterface;
use PSX\Base;
use PSX\Dispatch\FilterInterface;
use PSX\Exception;
use PSX\Http\Authentication;

/**
 * BasicAuthentication
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class BasicAuthentication implements FilterInterface
{
	protected $isValidCallback;
	protected $successCallback;
	protected $failureCallback;
	protected $missingCallback;

	/**
	 * The isValidCallback is called with the provided username and password
	 * if an Authorization header is present. Depending on the result the 
	 * onSuccess or onFailure callback is called. If the header is missing the
	 * onMissing callback is called
	 *
	 * @param Closure $isValidCallback
	 */
	public function __construct(Closure $isValidCallback)
	{
		$this->isValidCallback = $isValidCallback;

		$this->onSuccess(function(){
			// authentication successful
		});

		$this->onFailure(function(){
			throw new Exception('Invalid username or password');
		});

		$this->onMissing(function(ResponseInterface $response){
			$params = array(
				'realm' => 'psx',
			);

			$response->setStatusCode(401);
			$response->setHeader('WWW-Authenticate', 'Basic ' . Authentication::encodeParameters($params));

			throw new Exception('Missing authorization header');
		});
	}

	public function handle(RequestInterface $request, ResponseInterface $response)
	{
		$authorization = $request->getHeader('Authorization');

		if(!empty($authorization))
		{
			$parts = explode(' ', $authorization, 2);
			$type  = isset($parts[0]) ? $parts[0] : null;
			$data  = isset($parts[1]) ? $parts[1] : null;

			if($type == 'Basic' && !empty($data))
			{
				$data  = base64_decode($data);
				$parts = explode(':', $data, 2);

				$username = isset($parts[0]) ? $parts[0] : null;
				$password = isset($parts[1]) ? $parts[1] : null;
				$result   = call_user_func_array($this->isValidCallback, array($username, $password));

				if($result === true)
				{
					$this->callSuccess($response);
				}
				else
				{
					$this->callFailure($response);
				}
			}
			else
			{
				$this->callMissing($response);
			}
		}
		else
		{
			$this->callMissing($response);
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

	protected function callSuccess(ResponseInterface $response)
	{
		call_user_func_array($this->successCallback, array($response));
	}

	protected function callFailure(ResponseInterface $response)
	{
		call_user_func_array($this->failureCallback, array($response));
	}

	protected function callMissing(ResponseInterface $response)
	{
		call_user_func_array($this->missingCallback, array($response));
	}
}
