<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Oauth\Provider\Data;

/**
 * Consumer
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Consumer
{
	private $consumerKey;
	private $consumerSecret;
	private $token;
	private $tokenSecret;
	private $callback;

	public function __construct($consumerKey, $consumerSecret, $token = null, $tokenSecret = null, $callback = null)
	{
		$this->setConsumerKey($consumerKey);
		$this->setConsumerSecret($consumerSecret);
		$this->setToken($token);
		$this->setTokenSecret($tokenSecret);
		$this->setCallback($callback);
	}

	public function setConsumerKey($consumerKey)
	{
		$this->consumerKey = $consumerKey;
	}

	public function setConsumerSecret($consumerSecret)
	{
		$this->consumerSecret = $consumerSecret;
	}

	public function setToken($token)
	{
		$this->token = $token;
	}

	public function setTokenSecret($tokenSecret)
	{
		$this->tokenSecret = $tokenSecret;
	}

	public function setCallback($callback)
	{
		$this->callback = $callback;
	}

	public function getConsumerKey()
	{
		return $this->consumerKey;
	}

	public function getConsumerSecret()
	{
		return $this->consumerSecret;
	}

	public function getToken()
	{
		return $this->token;
	}

	public function getTokenSecret()
	{
		return $this->tokenSecret;
	}

	public function getCallback()
	{
		return $this->callback;
	}
}
