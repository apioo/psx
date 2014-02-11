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

namespace PSX\OpenId\Provider;

use PSX\Data\InvalidDataException;
use PSX\Url;
use PSX\OpenId;
use PSX\OpenId\ProviderAbstract;

/**
 * Redirect
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Redirect
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

	public function setOpEndpoint($opEndpoint)
	{
		$this->opEndpoint = new Url($opEndpoint);
	}

	public function setClaimedId($claimedId)
	{
		$this->claimedId = $claimedId;
	}

	public function setIdentity($identity)
	{
		$this->identity = $identity;
	}

	public function setReturnTo($returnTo)
	{
		$this->returnTo = $returnTo instanceof Url ? $returnTo : new Url($returnTo);
	}

	public function setResponseNonce($responseNonce)
	{
		$this->responseNonce = $responseNonce;
	}

	public function setInvalidateHandle($invalidateHandle)
	{
		$this->invalidateHandle = $invalidateHandle;
	}

	public function setAssocHandle($assocHandle)
	{
		$this->assocHandle = $assocHandle;
	}

	public function setSigned($signed)
	{
		$this->signed = explode(',', $signed);
	}

	public function setSig($sig)
	{
		$this->sig = $sig;
	}

	public function setParams(array $params)
	{
		// import only params wich dont have the prefix openid_
		$data = array();

		foreach($params as $k => $v)
		{
			if(substr($k, 0, 7) != 'openid_')
			{
				$data[$k] = $v;
			}
		}

		$this->params = $data;
	}

	public function addExtension(array $params)
	{
		$this->params = array_merge($this->params, $params);
	}

	public function getOpEndpoint()
	{
		return $this->opEndpoint;
	}

	public function getClaimedId()
	{
		return $this->claimedId;
	}

	public function getIdentity()
	{
		return $this->identity;
	}

	public function getReturnTo()
	{
		return $this->returnTo;
	}

	public function getResponseNonce()
	{
		return $this->responseNonce;
	}

	public function getInvalidateHandle()
	{
		return $this->invalidateHandle;
	}

	public function getAssocHandle()
	{
		return $this->assocHandle;
	}

	public function getSigned()
	{
		return $this->signed;
	}

	public function getSig()
	{
		return $this->sig;
	}

	public function getParams()
	{
		return $this->params;
	}

	/**
	 * Builds the url to redirect the user back to the relying party
	 *
	 * @return PSX\Url
	 */
	public function getUrl($secret, $assocType)
	{
		// build signature
		$params    = OpenId::extractParams($this->buildParams());
		$signed    = $this->getParamsToSign($params);
		$signature = OpenId::buildSignature($params, $signed, $secret, $assocType);

		$params = $this->buildParams();
		$params['openid.signed'] = implode(',', $signed);
		$params['openid.sig']    = $signature;

		// add params to url
		$url = $this->getReturnTo();

		if(empty($url))
		{
			throw new InvalidDataException('No return_to url was set');
		}

		foreach($params as $k => $v)
		{
			$url->addParam($k, $v);
		}

		return $url;
	}

	/**
	 * Returns an array containing keys wich should be signed. By default we
	 * sign all openid.* keys except for the "mode" key
	 *
	 * @return array
	 */
	protected function getParamsToSign(array $params)
	{
		if(isset($params['mode']))
		{
			unset($params['mode']);
		}

		return array_keys($params);
	}

	protected function buildParams()
	{
		// build basic params
		$params = array(
			'openid.ns'             => ProviderAbstract::NS,
			'openid.mode'           => 'id_res',
			'openid.op_endpoint'    => strval($this->getOpEndpoint()),
			'openid.return_to'      => strval($this->getReturnTo()),
			'openid.response_nonce' => $this->getResponseNonce(),
			'openid.assoc_handle'   => $this->getAssocHandle(),
		);

		$claimedId = $this->getClaimedId();
		$identity  = $this->getIdentity();

		if(!empty($claimedId) && !empty($identity))
		{
			$params['openid.claimed_id'] = $claimedId;
			$params['openid.identity']   = $identity;
		}

		// merge extensions we use + to combine the array because with
		// array_merge we would overwrite existing values
		$params = $params + (array) $this->getParams();

		return $params;
	}
}
