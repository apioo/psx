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

namespace PSX;

use PSX\Http\GetRequest;
use PSX\Hostmeta\DocumentAbstract;
use PSX\Hostmeta\Link;
use PSX\Webfinger\ResourceNotFoundException;

/**
 * Webfinger
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://tools.ietf.org/html/rfc7033
 */
class Webfinger
{
	protected $http;
	protected $hostmetaDiscovery = true;
	protected $lastError;

	public function __construct(Http $http)
	{
		$this->http = $http;
	}

	public function setHostmetaDiscovery($discovery)
	{
		$this->hostmetaDiscovery = (boolean) $discovery;
	}

	public function getLastError()
	{
		return $this->lastError;
	}

	/**
	 * Tries to obtain an JRD document for an specific resource. Looks up first
	 * the .well-known/webfinger endpoint if this fails checks whether a 
	 * .well-known/host-meta exists and uses lrdd discovery. The fallback 
	 * hostmeta discovery gets removed if enough provider support rfc7033.
	 *
	 * @return PSX\Hostmeta\DocumentAbstract
	 */
	public function discover(Url $url, $resource, $rel = null)
	{
		$jrd = $this->discoverWebfingerRfc7033($url, $resource, $rel);

		if($jrd instanceof DocumentAbstract)
		{
			return $jrd;
		}
		else if($this->hostmetaDiscovery)
		{
			$jrd = $this->discoverHostmetaRfc6415($url, $resource);

			if($jrd instanceof DocumentAbstract)
			{
				return $jrd;
			}
		}

		throw new ResourceNotFoundException(!empty($this->lastError) ? $this->lastError : 'Could not discover resource');
	}

	public function discoverByEmail($email, $rel = null)
	{
		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			throw new Exception('Invalid email format');
		}

		$host = substr(strstr($email, '@'), 1);
		$url  = new Url('http://' . $host);

		return $this->discover($url, 'acct:' . $email, $rel);
	}

	protected function discoverWebfingerRfc7033(Url $url, $resource, $rel = null)
	{
		try
		{
			$url = $url->getScheme() . '://' . $url->getHost() . '/.well-known/webfinger';
			$url = new Url($url);
			$url->addParam('resource', $resource);

			if($rel !== null)
			{
				$url->addParam('rel', $rel);
			}

			return Hostmeta::requestJrd($this->http, $url);
		}
		catch(\Exception $e)
		{
			$this->lastError = $e->getMessage();
		}

		return null;
	}

	protected function discoverHostmetaRfc6415(Url $url, $resource)
	{
		try
		{
			$hostmeta = new Hostmeta($this->http);
			$document = $hostmeta->discover($url);

			if($document instanceof DocumentAbstract)
			{
				$link = $document->getLinkByRel('lrdd');

				if($link instanceof Link)
				{
					$template = $link->getTemplate();

					if(!empty($template))
					{
						$lrddUrl = new Url(str_replace('{uri}', urlencode($resource), $template));

						return Hostmeta::requestJrd($this->http, $lrddUrl);
					}
				}
			}
		}
		catch(\Exception $e)
		{
			$this->lastError = $e->getMessage();
		}

		return null;
	}
}
