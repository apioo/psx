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

namespace PSX;

use PSX\Http\Request;
use PSX\Http\Stream\TempStream;
use PSX\Util\Uuid;
use UnexpectedValueException;

/**
 * Base
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Base
{
	const VERSION = '0.8.3';

	/**
	 * The current request method
	 *
	 * @var string
	 */
	private static $requestMethod;

	/**
	 * Indicates whether the method was overwritten or not
	 *
	 * @var boolean
	 */
	private static $methodOverride = false;

	/**
	 * Caches the request header if the getRequestHeader method is called
	 *
	 * @var array
	 */
	private static $headers;

	/**
	 * Contains the raw request
	 *
	 * @var string
	 */
	private static $rawInput;

	/**
	 * Contains the response code
	 *
	 * @var string
	 */
	private static $responseCode = null;

	/**
	 * Contains the absolute url to the script using the psx_url from the
	 * configuration
	 *
	 * @var string
	 */
	protected $self;

	/**
	 * The host of the value of psx_url
	 *
	 * @var string
	 */
	protected $host;

	/**
	 * The path of the value of psx_url
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Contains the current http request object
	 *
	 * @var PSX\Http\Request
	 */
	protected $request;

	protected $config;

	public function __construct(Config $config)
	{
		// set config
		$this->config = $config;

		// assign the host
		$parts = parse_url($this->config['psx_url']);

		if($parts !== false && isset($parts['scheme']) && isset($parts['host']))
		{
			$port = !empty($parts['port']) ? ':' . $parts['port'] : '';
			$path = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

			$this->self = $parts['scheme'] . '://' . $parts['host'] . $port . $path;
			$this->host = $parts['host'];
			$this->path = isset($parts['path']) ? $parts['path'] : '';
		}
		else
		{
			throw new UnexpectedValueException('Invalid PSX url');
		}
	}

	public function getConfig()
	{
		return $this->config;
	}

	/**
	 * Returns the absolute url of the current requested url
	 *
	 * @return string
	 */
	public function getSelf()
	{
		return $this->self;
	}

	/**
	 * Returns the host name of the url
	 *
	 * @return string
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * Returns the path of the url
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Generates an urn in the psx namespace for this host
	 *
	 * @return string
	 */
	public function getUrn()
	{
		return Urn::buildUrn(array_merge(array('psx', $this->host), func_get_args()));
	}

	/**
	 * Generates an tag uri based on the host
	 *
	 * @return string
	 */
	public function getTag(\DateTime $date, $specific)
	{
		return Uri::buildTag($this->host, $date, $specific);
	}

	/**
	 * Generates an Name-Based UUID where the namespace is the host of this
	 * domain
	 *
	 * @return string
	 */
	public function getUUID($name)
	{
		return Uuid::nameBased($this->host . $name);
	}

	/**
	 * Returns the version of the framework
	 *
	 * @return string
	 */
	public static function getVersion()
	{
		return self::VERSION;
	}
}
