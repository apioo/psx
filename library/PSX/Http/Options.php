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

namespace PSX\Http;

/**
 * Options
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Options
{
	protected $callback;
	protected $timeout;
	protected $followLocation = false;
	protected $maxRedirects   = 8;
	protected $ssl;
	protected $caPath;
	protected $proxy;

	/**
	 * Sets an callback which is called before the request is made. The first
	 * argument is the resource of the handler thus the client can configure 
	 * i.e. the curl resource
	 * 
	 * @param Closure $callback
	 */
	public function setCallback(\Closure $callback)
	{
		$this->callback = $callback;
	}

	/**
	 * @return Closure
	 */
	public function getCallback()
	{
		return $this->callback;
	}

	/**
	 * Sets the timeout in seconds
	 *
	 * @param integer $timeout
	 * @return void
	 */
	public function setTimeout($timeout)
	{
		$this->timeout = (int) $timeout;
	}

	/**
	 * Returns the timeout in seconds
	 *
	 * @return integer
	 */
	public function getTimeout()
	{
		return $this->timeout;
	}

	/**
	 * Sets whether the request should follow redirection headers
	 *
	 * @param boolean $location
	 * @param integer $maxRedirects
	 * @return void
	 */
	public function setFollowLocation($location, $maxRedirects = 8)
	{
		$this->followLocation = (bool) $location;
		$this->maxRedirects   = (int) $maxRedirects;
	}

	/**
	 * Returns whether the request follows redirection headers
	 *
	 * @return boolean
	 */
	public function getFollowLocation()
	{
		return $this->followLocation;
	}

	/**
	 * Returns how many redirects the request should follow
	 *
	 * @return integer
	 */
	public function getMaxRedirects()
	{
		return $this->maxRedirects;
	}

	/**
	 * Sets whether the request should be made through ssl
	 *
	 * @param boolean $ssl
	 * @param string $caPath
	 */
	public function setSsl($ssl, $caPath = null)
	{
		$this->ssl    = (bool) $ssl;
		$this->caPath = $caPath;
	}

	/**
	 * Returns whether the request should use ssl
	 *
	 * @return boolean
	 */
	public function getSsl()
	{
		return $this->ssl;
	}

	/**
	 * Returns the CA path
	 *
	 * @return string
	 */
	public function getCaPath()
	{
		return $this->caPath;
	}

	/**
	 * Sets whether an specific proxy should be used. The proxy should be in the
	 * format [ip]:[port]
	 *
	 * @param string $proxy
	 */
	public function setProxy($proxy)
	{
		$this->proxy = $proxy;
	}

	/**
	 * Returns the given proxy
	 *
	 * @return string
	 */
	public function getProxy()
	{
		return $this->proxy;
	}
}
