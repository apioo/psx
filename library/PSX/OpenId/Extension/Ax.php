<?php
/*
 *  $Id: Ax.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\OpenId\Extension;

use PSX\OpenId\ExtensionInterface;

/**
 * PSX_OpenId_Extension_Ax
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_OpenId
 * @version    $Revision: 480 $
 */
class Ax implements ExtensionInterface
{
	const NS = 'http://openid.net/srv/ax/1.0';

	private $required    = array();
	private $optional    = array();
	private $ifAvailable = array();

	public function __construct(array $required = array(), array $ifAvailable = array())
	{
		foreach($required as $name => $ns)
		{
			$this->addRequired($name, $ns);
		}

		foreach($ifAvailable as $name => $ns)
		{
			$this->addIfAvailable($name, $ns);
		}
	}

	/**
	 * Adds an required attribute that you want fetch from an openid request.
	 *
	 * @see http://www.axschema.org/types/
	 * @param string $name
	 * @param string $ns
	 * @return void
	 */
	public function addRequired($name, $ns)
	{
		$this->required[$name] = $ns;
	}

	public function addIfAvailable($name, $ns)
	{
		$this->ifAvailable[$name] = $ns;
	}

	public function getParams()
	{
		$params = array();

		$params['openid.ns.ax'] = self::NS;

		$params['openid.ax.mode'] = 'fetch_request';

		if(!empty($this->required))
		{
			$params['openid.ax.required'] = implode(',', array_keys($this->required));

			foreach($this->required as $name => $ns)
			{
				$params['openid.ax.type.' . $name] = $ns;
			}
		}

		if(!empty($this->ifAvailable))
		{
			$params['openid.ax.if_available'] = implode(',', array_keys($this->ifAvailable));

			foreach($this->ifAvailable as $name => $ns)
			{
				$params['openid.ax.type.' . $name] = $ns;
			}
		}

		return $params;
	}

	public function getNs()
	{
		return self::NS;
	}
}
