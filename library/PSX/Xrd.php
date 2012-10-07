<?php
/*
 *  $Id: Xrd.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

/**
 * PSX_Xrd
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Xrd
 * @version    $Revision: 480 $
 */
class PSX_Xrd
{
	public static $verifyAttrGroup         = array('absent', 'off', 'verified', 'failed');
	public static $selectionAttrGroupMatch = array('default', 'any', 'non-null', 'null');
	public static $appendAttrGroupAppend   = array('none', 'local', 'authority', 'path', 'query', 'qxri');

	public $query;
	public $providerid;
	public $redirect;
	public $ref;
	public $equivid;
	public $canonicalid;
	public $canonicalequivid;
	public $path;
	public $mediatype;
	public $uri;

	public $type         = array();
	public $status       = array();
	public $serverstatus = array();
	public $expires      = array();
	public $localid      = array();
	public $service      = array();

	public $raw;

	public function __construct(SimpleXMLElement $xrd)
	{
		$this->raw = $xrd;

		foreach($xrd->children() as $child)
		{
			$k = strtolower($child->getName());

			switch($k)
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

					$this->$k = strval($child);

					break;

				case 'type':
				case 'status':
				case 'serverstatus':
				case 'expires':
				case 'localid':
				case 'service':

					$class = 'PSX_Xrd_' . ucfirst($k);

					array_push($this->$k, new $class($child));

					break;
			}
		}
	}

	public function getType()
	{
		return $this->type;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function getServerStatus()
	{
		return $this->serverstatus;
	}

	public function getLocalId()
	{
		return $this->localid;
	}

	public function getService()
	{
		return $this->service;
	}

	public function getRaw()
	{
		return $this->raw;
	}

	public function sortServicePriority()
	{
		$this->sortService(0, count($this->services) - 1);
	}

	private function sortService($lft, $rgt)
	{
		if($lft < $rgt)
		{
			$divider = $this->split($lft, $rgt);

			$this->sortService($lft, $divider - 1);
			$this->sortService($divider + 1, $rgt);
		}
	}

	private function split($lft, $rgt)
	{
		$i = $lft;
		$j = $rgt - 1;

		$pivot = $this->services[$rgt]->getPriority();

		do
		{
			while($this->services[$i]->getPriority() < $pivot && $i < $rgt)
			{
				$i = $i + 1;
			}

			while($this->services[$j]->getPriority() > $pivot && $j > $lft)
			{
				$j = $j - 1;
			}

			if($i < $j)
			{
				$temp = $this->services[$j];

				$this->services[$j] = $this->services[$i];
				$this->services[$i] = $temp;
			}

		}
		while($i < $j);

		if($this->services[$i]->getPriority() > $pivot)
		{
			$temp = $this->services[$i];

			$this->services[$i]   = $this->services[$rgt];
			$this->services[$rgt] = $temp;
		}

		return $i;
	}

	public function __toString()
	{
		return $this->getRaw();
	}
}
