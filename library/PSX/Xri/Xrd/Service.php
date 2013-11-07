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

namespace PSX\Xri\Xrd;

use SimpleXMLElement;

/**
 * Service
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Service
{
	protected $providerid;
	protected $path;
	protected $mediatype;
	protected $redirect;
	protected $ref;
	protected $localid;
	protected $priority;

	protected $uri  = array();
	protected $type = array();

	public function __construct(SimpleXMLElement $service)
	{
		$this->priority = isset($service['priority']) ? intval($service['priority']) : 0;

		foreach($service->children() as $child)
		{
			$k = strtolower($child->getName());

			switch($k)
			{
				case 'providerid':
				case 'path':
				case 'mediatype':
				case 'redirect':
				case 'ref':
				case 'localid':
					$this->$k = trim(strval($child));
					break;

				case 'uri':
				case 'type':
					array_push($this->$k, trim(strval($child)));
					break;
			}
		}
	}

	public function getProviderId()
	{
		return $this->providerid;
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
		return current($this->uri);
	}

	public function getUris()
	{
		return $this->uri;
	}

	public function getRedirect()
	{
		return $this->redirect;
	}

	public function getRef()
	{
		return $this->ref;
	}

	public function getLocalId()
	{
		return $this->localid;
	}

	public function getPriority()
	{
		return $this->priority;
	}

	public function getType()
	{
		return $this->type;
	}

	public function hasType($type)
	{
		return in_array($type, $this->type);
	}
}

