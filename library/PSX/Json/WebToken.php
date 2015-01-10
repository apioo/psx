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

namespace PSX\Json;

use PSX\Exception;
use PSX\Json;

/**
 * WebToken
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     https://tools.ietf.org/html/draft-ietf-oauth-json-web-token-32
 */
abstract class WebToken
{
	const CLAIM_ISSUER     = 'iss';
	const CLAIM_SUBJECT    = 'sub';
	const CLAIM_AUDIENCE   = 'aud';
	const CLAIM_EXPIRATION = 'exp';
	const CLAIM_NOT_BEFORE = 'nbf';
	const CLAIM_ISSUED_AT  = 'iat';
	const CLAIM_JWT_ID     = 'jti';

	const TYPE             = 'typ';
	const CONTENT_TYPE     = 'cty';

	/**
	 * @var array
	 */
	protected $headers;

	/**
	 * @var string
	 */
	protected $claim;

	/**
	 * @var string
	 */
	protected $signature;

	public function __construct(array $headers = array(), $claim = null, $signature = null)
	{
		$this->setHeaders($headers);
		$this->setClaim($claim);
		$this->setSignature($signature);
	}

	public function setHeaders(array $headers)
	{
		$this->headers = $headers;
	}

	public function getHeaders()
	{
		return $this->headers;
	}

	public function setHeader($key, $value)
	{
		$this->headers[$key] = $value;
	}

	public function getHeader($key)
	{
		return isset($this->headers[$key]) ? $this->headers[$key] : null;
	}

	public function removeHeader($key)
	{
		unset($this->headers[$key]);
	}

	public function setClaim($claim)
	{
		$this->claim = $claim;
	}
	
	public function getClaim()
	{
		return $this->claim;
	}

	public function setSignature($signature)
	{
		$this->signature = $signature;
	}
	
	public function getSignature()
	{
		return $this->signature;
	}

	public static function parse($token)
	{
		$data      = (string) $token;
		$parts     = explode('.', $data, 3);

		$header    = isset($parts[0]) ? $parts[0] : null;
		$claim     = isset($parts[1]) ? $parts[1] : null;
		$signature = isset($parts[2]) ? $parts[2] : null;

		if(empty($header))
		{
			throw new Exception('JWT header not available');
		}

		if(empty($claim))
		{
			throw new Exception('JWT claim not available');
		}

		if(empty($signature))
		{
			throw new Exception('JWT signature not available');
		}

		// get typ from header
		$header = Json::decode(self::base64Decode($header));
		$claim  = self::base64Decode($claim);
		$type   = isset($header[self::TYPE]) ? strtoupper($header[self::TYPE]) : 'JWT';

		switch($type)
		{
			case 'JWS':
				return new WebSignature($header, $claim, $signature);
				break;

			case 'JWE':
				// @todo not implemented yet
				return null;
				break;

			case 'JWT':
			case 'urn:ietf:params:oauth:token-type:jwt':
			default:
				return new WebSignature($header, $claim, $signature);
				break;
		}
	}

	public static function base64Encode($data)
	{
		$data = base64_encode($data);
		$data = strtr($data, '+/', '-_');
		$data = rtrim($data, '=');

		return $data;
	}

	public static function base64Decode($data)
	{
		$data = strtr($data, '-_', '+/');

		switch(strlen($data) % 4)
		{
			case 0:
				break;
			case 2:
				$data.= '==';
				break;
			case 3:
				$data.= '=';
				break;
			default:
				throw new Exception('Illegal base64url string!');
		}

		return base64_decode($data);
	}
}
