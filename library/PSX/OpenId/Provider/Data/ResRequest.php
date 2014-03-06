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

namespace PSX\OpenId\Provider\Data;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;
use PSX\Data\InvalidDataException;
use PSX\Data\NotSupportedException;
use PSX\Data\ReaderInterface;
use PSX\Data\WriterInterface;
use PSX\Url;
use PSX\OpenId;

/**
 * ResRequest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ResRequest extends RecordAbstract
{
	protected $opEndpoint;
	protected $claimedId;
	protected $identity;
	protected $returnTo;
	protected $responseNonce;
	protected $invalidateHandle;
	protected $assocHandle;
	protected $signed;
	protected $sig;
	protected $params;

	private $map = array(
		'opEndpoint'       => 'op_endpoint',
		'claimedId'        => 'claimed_id',
		'identity'         => 'identity',
		'returnTo'         => 'return_to',
		'responseNonce'    => 'response_nonce',
		'invalidateHandle' => 'invalidate_handle',
		'assocHandle'      => 'assoc_handle',
		'signed'           => 'signed',
		'sig'              => 'sig',
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

	public function setOpEndpoint($opEndpoint)
	{
		$this->opEndpoint = new Url($opEndpoint);
	}

	public function getOpEndpoint()
	{
		return $this->opEndpoint;
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

	public function setReturnTo($returnTo)
	{
		$this->returnTo = new Url($returnTo);
	}

	public function getReturnTo()
	{
		return $this->returnTo;
	}

	public function setResponseNonce($responseNonce)
	{
		$this->responseNonce = $responseNonce;
	}

	public function getResponseNonce()
	{
		return $this->responseNonce;
	}

	public function setInvalidateHandle($invalidateHandle)
	{
		$this->invalidateHandle = $invalidateHandle;
	}

	public function getInvalidateHandle()
	{
		return $this->invalidateHandle;
	}

	public function setAssocHandle($assocHandle)
	{
		$this->assocHandle = $assocHandle;
	}

	public function getAssocHandle()
	{
		return $this->assocHandle;
	}

	public function setSigned($signed)
	{
		$this->signed = explode(',', $signed);
	}

	public function getSigned()
	{
		return $this->signed;
	}

	public function setSig($sig)
	{
		if(empty($sig))
		{
			throw new InvalidDataException('Signature must not be empty');
		}

		$this->sig = $sig;
	}

	public function getSig()
	{
		return $this->sig;
	}

	public function setParams(array $params)
	{
		$this->params = $params;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function isValidSignature($secret, $assocType)
	{
		$params     = OpenId::extractParams($this->params);
		$signature  = OpenId::buildSignature($params, $this->getSigned(), $secret, $assocType);
		$foreignSig = $this->getSig();

		return strcmp($foreignSig, $signature) === 0;
	}
}
