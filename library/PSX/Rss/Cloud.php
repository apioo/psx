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

namespace PSX\Rss;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Cloud
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Cloud extends RecordAbstract
{
	protected $domain;
	protected $port;
	protected $path;
	protected $registerProcedure;
	protected $protocol;

	public function __construct($domain = null, $port = null, $path = null, $registerProcedure = null, $protocol = null)
	{
		if($domain !== null)
		{
			$this->setDomain($domain);
		}

		if($port !== null)
		{
			$this->setPort($port);
		}

		if($path !== null)
		{
			$this->setPath($path);
		}

		if($registerProcedure !== null)
		{
			$this->setRegisterProcedure($registerProcedure);
		}

		if($protocol !== null)
		{
			$this->setProtocol($protocol);
		}
	}

	public function getRecordInfo()
	{
		return new RecordInfo('category', array(
			'domain'            => $this->domain,
			'port'              => $this->port,
			'path'              => $this->path,
			'registerProcedure' => $this->registerProcedure,
			'protocol'          => $this->protocol,
		));
	}

	public function setDomain($domain)
	{
		$this->domain = $domain;
	}
	
	public function getDomain()
	{
		return $this->domain;
	}

	public function setPort($port)
	{
		$this->port = $port;
	}
	
	public function getPort()
	{
		return $this->port;
	}

	public function setPath($path)
	{
		$this->path = $path;
	}
	
	public function getPath()
	{
		return $this->path;
	}

	public function setRegisterProcedure($registerProcedure)
	{
		$this->registerProcedure = $registerProcedure;
	}
	
	public function getRegisterProcedure()
	{
		return $this->registerProcedure;
	}

	public function setProtocol($protocol)
	{
		$this->protocol = $protocol;
	}
	
	public function getProtocol()
	{
		return $this->protocol;
	}
}
