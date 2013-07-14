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

namespace PSX\Json;

use PSX\Exception;
use PSX\Json;

/**
 * WebToken
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://tools.ietf.org/html/draft-jones-json-web-token-10
 */
class WebToken
{
	/**
	 * Contains the base64 decoded header
	 *
	 * @var string
	 */
	protected $header;

	/**
	 * Contains the base64 decoded payload
	 *
	 * @var string
	 */
	protected $payload;

	/**
	 * Contains the base64 encoded signature
	 *
	 * @var string
	 */
	protected $signature;

	/**
	 * Contains the json decoded header as array
	 *
	 * @var array
	 */
	protected $parameter = array();

	public function __construct($token = null)
	{
		if($token !== null)
		{
			$this->parse($token);
		}
	}

	public function setHeader($header)
	{
		$this->header = $header;
	}

	public function getHeader()
	{
		return $this->header;
	}

	public function setPayload($payload)
	{
		$this->payload = $payload;
	}
	
	public function getPayload()
	{
		return $this->payload;
	}

	public function setSignature($signature)
	{
		$this->signature = $signature;
	}
	
	public function getSignature()
	{
		return $this->signature;
	}

	public function setParameter(array $parameter)
	{
		$this->parameter = array_change_key_case($parameter);
	}

	public function getParameter($key)
	{
		return isset($this->parameter[$key]) ? $this->parameter[$key] : null;
	}

	public function addParameter($key, $value)
	{
		$this->parameter[$key] = $value;
	}

	public function removeParameter($key)
	{
		unset($this->parameter[$key]);
	}

	public function __call($name, $args)
	{
		$type = substr($name, 0, 3);
		$key  = strtolower(substr($name, 3));

		if($type == 'get')
		{
			return $this->getParameter($key);
		}
		else if($type == 'set')
		{
			return $this->addParameter($key, $args[0]);
		}
	}

	protected function parse($token)
	{
		$data      = (string) $token;
		$parts     = explode('.', $data);

		$header    = isset($parts[0]) ? $parts[0] : null;
		$payload   = isset($parts[1]) ? $parts[1] : null;
		$signature = isset($parts[2]) ? $parts[2] : null;

		if(empty($header))
		{
			throw new Exception('JWT header not available');
		}

		if(empty($payload))
		{
			throw new Exception('JWT payload not available');
		}

		if(empty($signature))
		{
			throw new Exception('JWT signature not available');
		}

		// set header
		$this->setHeader($this->base64Decode($header));

		// set payload
		$this->setPayload($this->base64Decode($payload));

		// set signature
		$this->setSignature($signature);

		// parse json header
		$parameter = Json::decode($this->getHeader());

		if(is_array($parameter))
		{
			$this->setParameter($parameter);
		}
		else
		{
			throw new Exception('Invalid header format');
		}
	}

	protected function base64Encode($data)
	{
		$data = base64_encode($data);
		$data = strtr($data, '+/', '-_');
		$data = rtrim($data, '=');

		return $data;
	}

	protected function base64Decode($data)
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

	public static function factory($token)
	{
		$data   = (string) $token;
		$parts  = explode('.', $data);
		$header = isset($parts[0]) ? $parts[0] : null;

		if(empty($header))
		{
			throw new Exception('JWT header not available');
		}

		// get typ from header
		$header = Json::decode($header);
		$type   = isset($header['typ']) ? $header['typ'] : 'JWT';

		switch($type)
		{
			case 'JWS':
				return new WebSignature($token);
				break;

			case 'JWE':
				// @todo not implemented yet
				return new WebToken($token);
				break;

			case 'JWT':
			case 'urn:ietf:params:oauth:token-type:jwt':
			default:
				return new WebToken($token);
				break;
		}
	}
}
