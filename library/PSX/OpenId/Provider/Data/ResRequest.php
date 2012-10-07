<?php
/*
 *  $Id: ResRequest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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
 * PSX_OpenId_Provider_Data_ResRequest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_OpenId
 * @version    $Revision: 480 $
 */
class PSX_OpenId_Provider_Data_ResRequest extends PSX_Data_RecordAbstract
{
	public $opEndpoint;
	public $claimedId;
	public $identity;
	public $returnTo;
	public $responseNonce;
	public $invalidateHandle;
	public $assocHandle;
	public $signed;
	public $sig;

	private $params;
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

	public function setOpEndpoint($opEndpoint)
	{
		$this->opEndpoint = new PSX_Url($opEndpoint);
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
		$this->returnTo = new PSX_Url($returnTo);
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
		if(empty($sig))
		{
			throw new PSX_Data_Exception('Signature must not be empty');
		}

		$this->sig = $sig;
	}

	public function setParams(array $params)
	{
		$this->params = $params;
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

	public function isValidSignature($secret, $assocType)
	{
		$params     = PSX_OpenId::extractParams($this->params);
		$signature  = PSX_OpenId::buildSignature($params, $this->getSigned(), $secret, $assocType);
		$foreignSig = $this->getSig();

		return strcmp($foreignSig, $signature) === 0;
	}

	public function import(PSX_Data_ReaderResult $result)
	{
		switch($result->getType())
		{
			case PSX_Data_ReaderInterface::GPC:

				$params = $result->getData();

				$this->setParams($params);

				if(isset($params['openid_op_endpoint']))
				{
					$this->setOpEndpoint($params['openid_op_endpoint']);
				}
				else
				{
					throw new PSX_Data_Exception('OP endpoint not set');
				}

				if(isset($params['openid_claimed_id']))
				{
					$this->setClaimedId($params['openid_claimed_id']);
				}

				if(isset($params['openid_identity']))
				{
					$this->setIdentity($params['openid_identity']);
				}

				if(isset($params['openid_return_to']))
				{
					$this->setReturnTo($params['openid_return_to']);
				}
				else
				{
					throw new PSX_Data_Exception('Return to not set');
				}

				if(isset($params['openid_response_nonce']))
				{
					$this->setResponseNonce($params['openid_response_nonce']);
				}
				else
				{
					throw new PSX_Data_Exception('Response nonce not set');
				}

				if(isset($params['openid_invalidate_handle']))
				{
					$this->setInvalidateHandle($params['openid_invalidate_handle']);
				}

				if(isset($params['openid_assoc_handle']))
				{
					$this->setAssocHandle($params['openid_assoc_handle']);
				}
				else
				{
					throw new PSX_Data_Exception('Assoc handle not set');
				}

				if(isset($params['openid_signed']))
				{
					$this->setSigned($params['openid_signed']);
				}
				else
				{
					throw new PSX_Data_Exception('Signed not set');
				}

				if(isset($params['openid_sig']))
				{
					$this->setSig($params['openid_sig']);
				}
				else
				{
					throw new PSX_Data_Exception('Sig not set');
				}

				break;

			default:

				throw new PSX_Data_Exception('Can only import data from reader GPC');

				break;
		}
	}

	public function export(PSX_Data_WriterResult $result)
	{
		switch($result->getType())
		{
			case PSX_Data_WriterInterface::FORM:
			case PSX_Data_WriterInterface::JSON:
			case PSX_Data_WriterInterface::XML:

				return $this->getData();

				break;
		}
	}
}
