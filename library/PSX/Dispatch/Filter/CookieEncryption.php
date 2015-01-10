<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use PSX\Dispatch\FilterChainInterface;
use PSX\Dispatch\FilterInterface;
use PSX\Exception;
use PSX\Http\CookieParser;
use PSX\Http\Exception\BadRequestException;
use PSX\Json;

/**
 * CookieEncryption
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CookieEncryption implements FilterInterface
{
	const COOKIE_NAME = 'psx_cookie';

	protected $secretKey;

	public function __construct($secretKey)
	{
		$this->secretKey = $secretKey;
	}

	public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain)
	{
		$signature = null;

		if($request->hasHeader('Cookie'))
		{
			$cookies = CookieParser::parseCookie($request->getHeader('Cookie'));

			foreach($cookies as $cookie)
			{
				if($cookie->getName() == self::COOKIE_NAME)
				{
					$data      = $cookie->getValue();
					$parts     = explode('.', $data, 2);

					$payload   = isset($parts[0]) ? $parts[0] : null;
					$signature = isset($parts[1]) ? $parts[1] : null;

					if(strcmp($signature, $this->generateSignature($payload)) === 0)
					{
						$request->setAttribute(self::COOKIE_NAME, $this->unserializeData($payload));
					}
					else
					{
						// invalid signature
					}

					break;
				}
			}
		}

		$filterChain->handle($request, $response);

		$data = $request->getAttribute(self::COOKIE_NAME);

		if(!empty($data))
		{
			$payload      = $this->serializeData($data);
			$newSignature = $this->generateSignature($payload);

			// send only a new cookie if the data has changed
			if($newSignature != $signature)
			{
				$response->addHeader('Set-Cookie', self::COOKIE_NAME . '=' . $payload . '.' . $newSignature);
			}
		}
	}

	protected function generateSignature($data)
	{
		return base64_encode(hash_hmac('sha256', $data, $this->secretKey, true));
	}

	protected function unserializeData($data)
	{
		try
		{
			return Json::decode(base64_decode($data));
		}
		catch(Exception $e)
		{
			return null;
		}
	}

	protected function serializeData($data)
	{
		return base64_encode(Json::encode($data));
	}
}
