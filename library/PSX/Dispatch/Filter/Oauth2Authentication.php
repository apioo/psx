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
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use PSX\Dispatch\FilterInterface;
use PSX\Exception;
use PSX\Http\Authentication;
use PSX\Http\Exception\UnauthorizedException;

/**
 * Oauth2Authentication
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Oauth2Authentication implements FilterInterface
{
	protected $accessCallback;
	protected $successCallback;
	protected $failureCallback;
	protected $missingCallback;

	/**
	 * The accessCallback is called with the provided access token. At the 
	 * moment this class supports only Bearer authentication. If the 
	 * accessCallback explicit return true the authorization was successful
	 *
	 * @param Closure $accessCallback
	 */
	public function __construct(Closure $accessCallback)
	{
		$this->accessCallback = $accessCallback;

		$this->onSuccess(function(){
			// authentication successful
		});

		$this->onFailure(function(){
			throw new Exception('Invalid access token');
		});

		$this->onMissing(function(ResponseInterface $response){
			$params = array(
				'realm' => 'psx',
			);

			throw new UnauthorizedException('Missing authorization header', 'Bearer', $params);
		});
	}

	public function handle(RequestInterface $request, ResponseInterface $response)
	{
		$authorization = $request->getHeader('Authorization');

		if(!empty($authorization))
		{
			$parts       = explode(' ', $authorization, 2);
			$type        = isset($parts[0]) ? $parts[0] : null;
			$accessToken = isset($parts[1]) ? $parts[1] : null;

			if($type == 'Bearer' && !empty($accessToken))
			{
				$result = call_user_func_array($this->accessCallback, array($accessToken));

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
