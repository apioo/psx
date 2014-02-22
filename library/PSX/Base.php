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
	const VERSION = '0.8.4';

	protected $host;
	protected $config;

	public function __construct(Config $config)
	{
		$this->config = $config;
		$this->host   = parse_url($this->config['psx_url'], PHP_URL_HOST);
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
