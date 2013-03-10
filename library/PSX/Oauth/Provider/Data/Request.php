<?php
/*
 *  $Id: Request.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

namespace PSX\Oauth\Provider\Data;

use PSX\Data\ReaderResult;
use PSX\Data\ReaderInterface;
use PSX\Data\RecordAbstract;
use PSX\Data\InvalidDataException;
use PSX\Data\NotSupportedException;
use PSX\Filter\Digit;
use PSX\Filter\Length;
use PSX\Filter\Url;
use PSX\Oauth;
use PSX\Validate;

/**
 * PSX_Oauth_Provider_Data_Request
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Oauth
 * @version    $Revision: 480 $
 */
class Request extends RecordAbstract
{
	public $consumerKey;
	public $token;
	public $signatureMethod;
	public $signature;
	public $timestamp;
	public $nonce;
	public $callback;
	public $version;
	public $verifier;

	private $validate;
	private $requiredFields;
	private $map = array(

		'consumerKey'     => 'consumer_key',
		'token'           => 'token',
		'signatureMethod' => 'signature_method',
		'signature'       => 'signature',
		'timestamp'       => 'timestamp',
		'nonce'           => 'nonce',
		'callback'        => 'callback',
		'version'         => 'version',
		'verifier'        => 'verifier'

	);

	public function __construct()
	{
		$this->validate = new Validate();
	}

	public function getName()
	{
		return 'request';
	}

	public function getFields()
	{
		$fields = array();

		foreach($this->map as $k => $v)
		{
			$key   = 'oauth_' . $v;
			$value = $this->$k;

			if(!empty($value))
			{
				$fields[$key] = $value;
			}
		}

		return $fields;
	}

	public function setConsumerKey($consumerKey)
	{
		$this->consumerKey = $consumerKey;
	}

	public function setToken($token)
	{
		$this->token = $token;
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

	public function setSignature($signature)
	{
		$this->signature = $signature;
	}

	public function setTimestamp($timestamp)
	{
		// currenty a timestamp has 10 signs i.e. 1256852759 in ca 300
		// years we get a timestamp that has 11 signs ... because of
		// that we check without considering whether the timestamp has
		// 10 signs ... should be future safe xD ... if many websites
		// uses in the future psx and they get problems because of that
		// ... shame on me ^^
		$timestamp = $this->validate->apply($timestamp, 'string', array(new Length(10, 10), new Digit()), 'timestamp', 'Timestamp');

		if(!$this->validate->hasError())
		{
			$this->timestamp = $timestamp;
		}
		else
		{
			throw new InvalidDataException($this->validate->getLastError());
		}
	}

	public function setNonce($nonce)
	{
		$this->nonce = $nonce;
	}

	public function setCallback($callback)
	{
		if($callback == 'oob')
		{
			// callback was set "out of bound" ... we get the url
			// later from the consumer object
			$this->callback = 'oob';
		}
		else
		{
			$callback = $this->validate->apply($callback, 'string', array(new Length(7, 256), new Url()), 'callback', 'Callback');

			if(!$this->validate->hasError())
			{
				$this->callback = $callback;
			}
			else
			{
				throw new InvalidDataException($this->validate->getLastError());
			}
		}
	}

	public function setVersion($version)
	{
		$this->version = $version;
	}

	public function setVerifier($verifier)
	{
		$verifier = $this->validate->apply($verifier, 'string', array(new Length(16, 512)), 'verifier', 'Verifier');

		if(!$this->validate->hasError())
		{
			$this->verifier = $verifier;
		}
		else
		{
			throw new InvalidDataException($this->validate->getLastError());
		}
	}

	public function getConsumerKey()
	{
		return $this->consumerKey;
	}

	public function getToken()
	{
		return $this->token;
	}

	public function getSignatureMethod()
	{
		return $this->signatureMethod;
	}

	public function getSignature()
	{
		return $this->signature;
	}

	public function getTimestamp()
	{
		return $this->timestamp;
	}

	public function getNonce()
	{
		return $this->nonce;
	}

	public function getCallback()
	{
		return $this->callback;
	}

	public function getVersion()
	{
		return $this->version;
	}

	public function getVerifier()
	{
		return $this->verifier;
	}

	public function setRequiredFields(array $requiredFields)
	{
		$this->requiredFields = $requiredFields;
	}

	public function import(ReaderResult $result)
	{
		switch($result->getType())
		{
			case ReaderInterface::RAW:

				$header = $result->getData()->getHeader();

				if(isset($header['authorization']))
				{
					$auth = $header['authorization'];

					if(strpos($auth, 'OAuth') !== false)
					{
						// get oauth data
						$data  = array();
						$items = explode(',', $auth);

						foreach($items as $v)
						{
							$v = trim($v);

							if(substr($v, 0, 6) == 'oauth_')
							{
								$pair = explode('=', $v);

								if(isset($pair[0]) && isset($pair[1]))
								{
									$key = substr(strtolower($pair[0]), 6);
									$val = trim($pair[1], '"');

									$data[$key] = Oauth::urlDecode($val);
								}
							}
						}


						// check whether all required values are available
						foreach($this->map as $k => $v)
						{
							if(isset($data[$v]))
							{
								$method = 'set' . ucfirst($k);

								if(is_callable(array($this, $method)))
								{
									$this->$method($data[$v]);
								}
								else
								{
									throw new InvalidDataException('Unknown parameter');
								}
							}
							else if(in_array($k, $this->requiredFields))
							{
								throw new InvalidDataException('Required parameter "' . $v . '" is missing');
							}
						}
					}
					else
					{
						throw new InvalidDataException('Unknown OAuth authentication');
					}
				}
				else
				{
					throw new InvalidDataException('Missing Authorization header');
				}

				break;

			default:

				throw new NotSupportedException('Can only import data from reader Raw');

				break;
		}
	}
}


