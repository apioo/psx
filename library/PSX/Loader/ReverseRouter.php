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

namespace PSX\Loader;

use InvalidArgumentException;

/**
 * ReverseRouter
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ReverseRouter
{
	protected $routingParser;
	protected $url;
	protected $dispatch;

	public function __construct(RoutingParserInterface $routingParser, $url, $dispatch)
	{
		$this->routingParser = $routingParser;
		$this->url           = $url;
		$this->basePath      = parse_url($this->url, PHP_URL_PATH);
		$this->dispatch      = $dispatch;
	}

	public function getPath($source, array $parameters = array(), $leadingPath = true)
	{
		$path  = $this->getPathBySource($source);
		$path  = explode('/', trim($path, '/'));
		$parts = array();
		$i     = 0;

		foreach($path as $key => $part)
		{
			if(isset($part[0]) && ($part[0] == ':' || $part[0] == '*'))
			{
				$name = substr($part, 1);

				if(isset($parameters[$name]))
				{
					$parts[] = $parameters[$name];
				}
				else if(isset($parameters[$i]))
				{
					$parts[] = $parameters[$i];
				}
				else
				{
					throw new InvalidArgumentException('Missing parameter ' . $name);
				}

				$i++;
			}
			else if(isset($part[0]) && $part[0] == '$')
			{
				$pos  = strpos($part, '<');
				$name = substr($part, 1, $pos - 1);
				$rexp = substr($part, $pos + 1, -1);

				if(isset($parameters[$name]) && preg_match('/' . $rexp . '/', $parameters[$name]))
				{
					$parts[] = $parameters[$name];
				}
				else if(isset($parameters[$i]) && preg_match('/' . $rexp . '/', $parameters[$i]))
				{
					$parts[] = $parameters[$i];
				}
				else
				{
					throw new InvalidArgumentException('Missing parameter ' . $name);
				}

				$i++;
			}
			else
			{
				$parts[] = $part;
			}
		}

		$path = implode('/', $parts);

		if($this->isAbsoluteUrl($path))
		{
			return $path;
		}

		return ($leadingPath ? '/' : '') . $path;
	}

	public function getBasePath()
	{
		return $this->basePath;
	}

	public function getDispatchUrl()
	{
		return $this->url . '/' . $this->dispatch;
	}

	public function getAbsolutePath($source, array $parameters = array())
	{
		$path = $this->getPath($source, $parameters, false);

		if($this->isAbsoluteUrl($path))
		{
			return $path;
		}
		else
		{
			return $this->basePath . '/' . $this->dispatch . $path;
		}
	}

	public function getUrl($source, array $parameters = array())
	{
		$path = $this->getPath($source, $parameters, false);

		if($this->isAbsoluteUrl($path))
		{
			return $path;
		}
		else
		{
			return $this->getDispatchUrl() . $path;
		}
	}

	protected function getPathBySource($source)
	{
		$routingCollection = $this->routingParser->getCollection();

		foreach($routingCollection as $routing)
		{
			if($routing[RoutingCollection::ROUTING_SOURCE] == $source)
			{
				return $routing[RoutingCollection::ROUTING_PATH];
			}
		}

		return null;
	}

	protected function isAbsoluteUrl($path)
	{
		return substr($path, 0, 7) == 'http://' || substr($path, 0, 8) == 'https://';
	}
}
