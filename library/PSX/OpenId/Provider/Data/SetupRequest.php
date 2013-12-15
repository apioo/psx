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

namespace PSX\OpenId\Provider\Data;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;
use PSX\Data\NotSupportedException;
use PSX\Data\ReaderInterface;
use PSX\Data\WriterInterface;
use PSX\OpenId\ProviderAbstract;
use PSX\Url;

/**
 * SetupRequest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SetupRequest extends RecordAbstract
{
	protected $claimedId;
	protected $identity;
	protected $assocHandle;
	protected $returnTo;
	protected $realm;
	protected $isImmediate;
	protected $params;

	private $map = array(
		'claimedId'   => 'claimed_id',
		'identity'    => 'identity',
		'assocHandle' => 'assoc_handle',
		'returnTo'    => 'return_to',
		'realm'       => 'realm',
	);

	public function getRecordInfo()
	{
		$fields = array();

		foreach($this->map as $k => $v)
		{
			$value = $this->$k;

			if(!empty($value))
			{
				$fields[$v] = $value;
			}
		}

		return new RecordInfo('request', $fields);
	}

	public function setClaimedId($claimedId)
	{
		$this->claimedId = $claimedId;
	}

	public function getClaimedId()
	{
		return $this->claimedId;
	}

	public function setIdentity($identity)
	{
		$this->identity = $identity;
	}

	public function getIdentity()
	{
		return $this->identity;
	}

	public function setAssocHandle($assocHandle)
	{
		$this->assocHandle = $assocHandle;
	}

	public function getAssocHandle()
	{
		return $this->assocHandle;
	}

	public function setReturnTo($returnTo)
	{
		$this->returnTo = new Url($returnTo);
	}

	public function getReturnTo()
	{
		return $this->returnTo;
	}

	public function setRealm($realm)
	{
		$this->realm = $realm;
	}

	public function getRealm()
	{
		return $this->realm;
	}

	public function setImmediate($immediate)
	{
		$this->isImmediate = (boolean) $immediate;
	}

	public function isImmediate()
	{
		return $this->isImmediate;
	}

	public function setParams(array $params)
	{
		$this->params = $params;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function getExtension($ns)
	{
		return ProviderAbstract::getExtension($this->params, $ns);
	}
}
