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

namespace PSX\OpenId\Store;

use PSX\OpenId\StoreInterface;
use PSX\OpenId\Provider\Association;

/**
 * Memory
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Memory implements StoreInterface
{
	protected $container = array();

	public function load($opEndpoint)
	{
		$key = md5($opEndpoint);
		$row = isset($this->container[$key]) ? $this->container[$key] : null;

		if(!empty($row))
		{
			$assoc = new Association();
			$assoc->setAssocHandle($row['assocHandle']);
			$assoc->setAssocType($row['assocType']);
			$assoc->setSessionType($row['sessionType']);
			$assoc->setSecret($row['secret']);
			$assoc->setExpire($row['expires']);

			return $assoc;
		}

		return null;
	}

	public function loadByHandle($opEndpoint, $assocHandle)
	{
		$key = md5($opEndpoint);
		$row = isset($this->container[$key]) ? $this->container[$key] : null;

		if(!empty($row) && $row['assocHandle'] == $assocHandle)
		{
			$assoc = new Association();
			$assoc->setAssocHandle($row['assocHandle']);
			$assoc->setAssocType($row['assocType']);
			$assoc->setSessionType($row['sessionType']);
			$assoc->setSecret($row['secret']);
			$assoc->setExpire($row['expires']);

			return $assoc;
		}

		return null;
	}

	public function remove($opEndpoint, $assocHandle)
	{
		$key = md5($opEndpoint);
		$row = isset($this->container[$key]) ? $this->container[$key] : null;

		if(!empty($row) && $row['assocHandle'] == $assocHandle)
		{
			unset($this->container[$key]);
		}
	}

	public function save($opEndpoint, Association $assoc)
	{
		$key = md5($opEndpoint);

		$this->container[$key] = array(
			'opEndpoint'  => $opEndpoint,
			'assocHandle' => $assoc->getAssocHandle(),
			'assocType'   => $assoc->getAssocType(),
			'sessionType' => $assoc->getSessionType(),
			'secret'      => $assoc->getSecret(),
			'expires'     => $assoc->getExpire(),
		);
	}
}
