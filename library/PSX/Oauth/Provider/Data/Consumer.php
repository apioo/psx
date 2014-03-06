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

namespace PSX\Oauth\Provider\Data;

/**
 * Consumer
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
