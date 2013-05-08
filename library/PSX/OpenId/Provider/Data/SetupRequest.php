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
use PSX\Data\NotSupportedException;
use PSX\Data\ReaderResult;
use PSX\Data\ReaderInterface;
use PSX\Data\WriterResult;
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
	public $claimedId;
	public $identity;
	public $assocHandle;
	public $returnTo;
	public $realm;
	public $isImmediate;

	private $params;
	private $map = array(

		'claimedId'   => 'claimed_id',
		'identity'    => 'identity',
		'assocHandle' => 'assoc_handle',
		'returnTo'    => 'return_to',
		'realm'       => 'realm',

	);

	public function getName()
	{
		return 'request';
	}

	public function getFields()
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

		return $fields;
	}

	public function setClaimedId($claimedId)
	{
		$this->claimedId = $claimedId;
	}

	public function setIdentity($identity)
	{
		$this->identity = $identity;
	}

	public function setAssocHandle($assocHandle)
	{
		$this->assocHandle = $assocHandle;
	}

	public function setReturnTo($returnTo)
	{
		$this->returnTo = new Url($returnTo);
	}

	public function setRealm($realm)
	{
		$this->realm = $realm;
	}

	public function setImmediate($immediate)
	{
		$this->isImmediate = (boolean) $immediate;
	}

	public function getClaimedId()
	{
		return $this->claimedId;
	}

	public function getIdentity()
	{
		return $this->identity;
	}

	public function getAssocHandle()
	{
		return $this->assocHandle;
	}

	public function getReturnTo()
	{
		return $this->returnTo;
	}

	public function getRealm()
	{
		return $this->realm;
	}

	public function isImmediate()
	{
		return $this->isImmediate;
	}

	public function getExtension($ns)
	{
		return ProviderAbstract::getExtension($this->params, $ns);
	}

	public function setParams(array $params)
	{
		$this->params = $params;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function import(ReaderResult $result)
	{
		switch($result->getType())
		{
			case ReaderInterface::GPC:

				$params = $result->getData();

				$this->setParams($params);

				if(isset($params['openid_claimed_id']))
				{
					$this->setClaimedId($params['openid_claimed_id']);
				}

				if(isset($params['openid_identity']))
				{
					$this->setIdentity($params['openid_identity']);
				}

				if(isset($params['openid_assoc_handle']))
				{
					$this->setAssocHandle($params['openid_assoc_handle']);
				}

				if(isset($params['openid_return_to']))
				{
					$this->setReturnTo($params['openid_return_to']);
				}

				if(isset($params['openid_realm']))
				{
					$this->setRealm($params['openid_realm']);
				}

				break;

			default:

				throw new NotSupportedException('Can only import data from reader Raw');

				break;
		}
	}

	public function export(WriterResult $result)
	{
		switch($result->getType())
		{
			case WriterInterface::FORM:
			case WriterInterface::JSON:
			case WriterInterface::XML:

				return $this->getData();

				break;
		}
	}
}
