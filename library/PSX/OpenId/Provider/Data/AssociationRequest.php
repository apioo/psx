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

use PSX\Data\InvalidDataException;
use PSX\Data\NotSupportedException;
use PSX\Data\RecordAbstract;
use PSX\Data\ReaderResult;
use PSX\Data\ReaderInterface;
use PSX\Data\WriterResult;
use PSX\Data\WriterInterface;
use PSX\OpenId;
use PSX\OpenId\ProviderAbstract;
use PSX\OpenId\Provider\Association;

/**
 * AssociationRequest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class AssociationRequest extends RecordAbstract
{
	public $assocType;
	public $sessionType;
	public $dhModulus;
	public $dhGen;
	public $dhConsumerPublic;

	public $assocHandle;
	public $macKey;
	public $dhServerPublic;
	public $encMacKey;

	private $assoc;

	public function getName()
	{
		return 'request';
	}

	public function getFields()
	{
		$fields = array();
		$fields['assoc_handle'] = $this->assocHandle;
		$fields['session_type'] = $this->sessionType;
		$fields['assoc_type']   = $this->assocType;

		switch($this->sessionType)
		{
			case 'no-encryption':

				$fields['mac_key'] = $this->macKey;
				break;

			case 'DH-SHA1':
			case 'DH-SHA256':

				$fields['dh_server_public'] = $this->dhServerPublic;
				$fields['enc_mac_key']      = $this->encMacKey;
				break;

			default:

				throw new InvalidDataException('Invalid session type');
				break;
		}

		return $fields;
	}

	public function setAssocType($assocType)
	{
		if(in_array($assocType, OpenId::$supportedAssocTypes))
		{
			$this->assocType = $assocType;
		}
		else
		{
			throw new InvalidDataException('Invalid association type');
		}
	}

	public function setSessionType($sessionType)
	{
		if(in_array($sessionType, OpenId::$supportedSessionTypes))
		{
			$this->sessionType = $sessionType;
		}
		else
		{
			throw new InvalidDataException('Invalid session type');
		}
	}

	public function setDhModulus($modulus)
	{
		$this->dhModulus = $modulus;
	}

	public function setDhGen($gen)
	{
		$this->dhGen = $gen;
	}

	public function setDhConsumerPublic($consumerPublic)
	{
		$this->dhConsumerPublic = $consumerPublic;
	}

	public function getAssocType()
	{
		return $this->assocType;
	}

	public function getSessionType()
	{
		return $this->sessionType;
	}

	public function getDhModulus()
	{
		return $this->dhModulus;
	}

	public function getDhGen()
	{
		return $this->dhGen;
	}

	public function getDhConsumerPublic()
	{
		return $this->dhConsumerPublic;
	}

	public function import(ReaderResult $result)
	{
		switch($result->getType())
		{
			case ReaderInterface::GPC:

				$params = $result->getData();

				if(isset($params['openid_assoc_type']))
				{
					$this->setAssocType($params['openid_assoc_type']);
				}

				if(isset($params['openid_session_type']))
				{
					$this->setSessionType($params['openid_session_type']);
				}

				if(isset($params['openid_dh_modulus']))
				{
					$this->setDhModulus($params['openid_dh_modulus']);
				}

				if(isset($params['openid_dh_gen']))
				{
					$this->setDhGen($params['openid_dh_gen']);
				}

				if(isset($params['openid_dh_consumer_public']))
				{
					$this->setDhConsumerPublic($params['openid_dh_consumer_public']);
				}

				$this->generateAssociation();

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

	public function getAssociation()
	{
		return $this->assoc;
	}

	private function generateAssociation()
	{
		// generate secret
		switch($this->assocType)
		{
			case 'HMAC-SHA1':

				$secret  = ProviderAbstract::randomBytes(20);
				$macFunc = 'SHA1';
				break;

			case 'HMAC-SHA256':

				$secret  = ProviderAbstract::randomBytes(32);
				$macFunc = 'SHA256';
				break;

			default:

				throw new InvalidDataException('Invalid association type');
				break;
		}

		// generate dh
		switch($this->sessionType)
		{
			case 'no-encryption':

				// $secret = base64_encode($secret);
				// $this->macKey = $secret;
				throw new InvalidDataException('no-encryption not supported');
				break;

			case 'DH-SHA1':

				$dh = ProviderAbstract::generateDh($this->getDhGen(), $this->getDhModulus(), $this->getDhConsumerPublic(), $macFunc, $secret);

				$this->dhServerPublic = $dh['pubKey'];
				$this->encMacKey      = $dh['macKey'];
				break;

			case 'DH-SHA256':

				$dh = ProviderAbstract::generateDh($this->getDhGen(), $this->getDhModulus(), $this->getDhConsumerPublic(), $macFunc, $secret);

				$this->dhServerPublic = $dh['pubKey'];
				$this->encMacKey      = $dh['macKey'];
				break;

			default:

				throw new InvalidDataException('Invalid association type');
				break;
		}

		$this->assocHandle = ProviderAbstract::generateHandle();

		$this->assoc = new Association();
		$this->assoc->setAssocHandle($this->assocHandle);
		$this->assoc->setAssocType($this->assocType);
		$this->assoc->setSessionType($this->sessionType);
		$this->assoc->setSecret(base64_encode($secret));
	}
}
