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

namespace PSX\Xri;

use SimpleXMLElement;

/**
 * Xrd
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Xrd
{
	protected $query;
	protected $providerid;
	protected $redirect;
	protected $ref;
	protected $equivid;
	protected $canonicalid;
	protected $canonicalequivid;
	protected $path;
	protected $mediatype;
	protected $uri;
	protected $status;
	protected $serverstatus;
	protected $expires;
	protected $localid;
	protected $service = array();

	protected $xml;

	public function __construct(SimpleXMLElement $xml)
	{
		$this->xml = $xml;

		foreach($xml->children() as $child)
		{
			$key = strtolower($child->getName());

			switch($key)
			{
				case 'query':
				case 'providerid':
				case 'redirect':
				case 'ref':
				case 'equivid':
				case 'canonicalid':
				case 'canonicalequivid':
				case 'path':
				case 'mediatype':
				case 'uri':
				case 'localid':
					$this->$key = trim(strval($child));
					break;

				case 'status':
				case 'serverstatus':
				case 'expires':
					$class = '\PSX\Xri\Xrd\\' . ucfirst($key);
					$this->$key = new $class($child);
					break;

				case 'service':
					$class = '\PSX\Xri\Xrd\\' . ucfirst($key);
					array_push($this->$key, new $class($child));
					break;
			}
		}
	}

	public function getQuery()
	{
		return $this->query;
	}

	public function getProviderId()
	{
		return $this->providerid;
	}

	public function getRedirect()
	{
		return $this->redirect;
	}

	public function getRef()
	{
		return $this->ref;
	}

	public function getEquivId()
	{
		return $this->equivid;
	}

	public function getCanonicalId()
	{
		return $this->canonicalid;
	}

	public function getCanonicalEquivId()
	{
		return $this->canonicalequivid;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getMediaType()
	{
		return $this->mediatype;
	}

	public function getUri()
	{
		return $this->uri;
	}

	/**
	 * @return PSX\Xrd\Status
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @return PSX\Xrd\Serverstatus
	 */
	public function getServerStatus()
	{
		return $this->serverstatus;
	}

	/**
	 * @return PSX\Xrd\Expires
	 */
	public function getExpires()
	{
		return $this->expires;
	}

	public function getLocalId()
	{
		return $this->localid;
	}

	/**
	 * @return PSX\Xrd\Service
	 */
	public function getService()
	{
		return $this->service;
	}

	/**
	 * @return PSX\Xrd\Service
	 */
	public function getServiceByType($type)
	{
		foreach($this->service as $service)
		{
			if($service->hasType($type))
			{
				return $service;
			}
		}

		return null;
	}

	public function getXml()
	{
		return $this->xml;
	}

	public static function fromXrds(SimpleXMLElement $xml)
	{
		$redirect = isset($xml['redirect']) ? strval($xml['redirect']) : null;
		$ref      = isset($xml['ref'])      ? strval($xml['ref'])      : null;

		if(isset($xml->XRD))
		{
			return new Xrd($xml->XRD);
		}
	}
}
