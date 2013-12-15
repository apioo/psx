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

namespace PSX\Oauth\Provider\Data;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;
use PSX\Data\ReaderInterface;
use PSX\Data\InvalidDataException;
use PSX\Data\NotSupportedException;
use PSX\Filter\Digit;
use PSX\Filter\Length;
use PSX\Filter\Url;
use PSX\Oauth;
use PSX\Validate;

/**
 * Request
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Request extends RecordAbstract
{
	protected $consumerKey;
	protected $token;
	protected $signatureMethod;
	protected $signature;
	protected $timestamp;
	protected $nonce;
	protected $callback;
	protected $version;
	protected $verifier;

	public function getRecordInfo()
	{
		return new RecordInfo('request', array(
			'consumer_key'     => $this->consumerKey,
			'token'            => $this->token,
			'signature_method' => $this->signatureMethod,
			'signature'        => $this->signature,
			'timestamp'        => $this->timestamp,
			'nonce'            => $this->nonce,
			'callback'         => $this->callback,
			'version'          => $this->version,
			'verifier'         => $this->verifier
		));
	}

	public function setConsumerKey($consumerKey)
	{
		$this->consumerKey = $consumerKey;
	}
	
	public function getConsumerKey()
	{
		return $this->consumerKey;
	}

	public function setToken($token)
	{
		$this->token = $token;
	}
	
	public function getToken()
	{
		return $this->token;
	}

	public function setSignatureMethod($signatureMethod)
	{
		switch($signatureMethod)
		{
			case 'HMAC-SHA1':
			case 'RSA-SHA1':
			case 'PLAINTEXT':
				$this->signatureMethod = $signatureMethod;
				break;

			default:
				throw new InvalidDataException('Invalid signature method');
				break;
		}
	}

	public function getSignatureMethod()
	{
		return $this->signatureMethod;
	}

	public function setSignature($signature)
	{
		$this->signature = $signature;
	}

	public function getSignature()
	{
		return $this->signature;
	}

	/**
	 * Currenty a timestamp has 10 signs i.e. 1256852759 in ca 300 years we get 
	 * a timestamp that has 11 signs ... because of that we check without 
	 * consideration whether the timestamp has 10 signs ... should be future 
	 * safe if many websites uses in the future psx and they get problems 
	 * because of that ... shame on me ^^
	 *
	 * @param integer $timestamp
	 */
	public function setTimestamp($timestamp)
	{
		if(is_numeric($timestamp) && strlen($timestamp) == 10)
		{
			$this->timestamp = $timestamp;
		}
		else
		{
			throw new InvalidDataException('Invalid timestamp format');
		}
	}

	public function getTimestamp()
	{
		return $this->timestamp;
	}

	public function setNonce($nonce)
	{
		$this->nonce = $nonce;
	}

	public function getNonce()
	{
		return $this->nonce;
	}

	public function setCallback($callback)
	{
		if($callback == 'oob')
		{
			// callback was set "out of bound" ... we get the url later from the 
			// consumer object
			$this->callback = 'oob';
		}
		else if(strlen($callback) >= 7 && strlen($callback) <= 256 && filter_var($callback, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED))
		{
			$this->callback = $callback;
		}
		else
		{
			throw new InvalidDataException('Invalid callback format');
		}
	}

	public function getCallback()
	{
		return $this->callback;
	}

	public function setVersion($version)
	{
		$this->version = $version;
	}

	public function getVersion()
	{
		return $this->version;
	}

	public function setVerifier($verifier)
	{
		if(strlen($verifier) >= 16 && strlen($verifier) <= 512)
		{
			$this->verifier = $verifier;
		}
		else
		{
			throw new InvalidDataException('Invalid verifier format');
		}
	}

	public function getVerifier()
	{
		return $this->verifier;
	}

	public function setRequiredFields(array $requiredFields)
	{
		$this->requiredFields = $requiredFields;
	}
}


