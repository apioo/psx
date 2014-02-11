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
use PSX\OpenId\ProviderAbstract;
use PSX\OpenId\Provider\Data\AssociationRequest;

/**
 * AssociationGenerator
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class AssociationGenerator
{
	protected $dhServerPublic;
	protected $encMacKey;
	protected $assocHandle;
	protected $secret;
	protected $macFunc;

	public function getDhServerPublic()
	{
		return $this->dhServerPublic;
	}

	public function getEncMacKey()
	{
		return $this->encMacKey;
	}

	public function getAssocHandle()
	{
		return $this->assocHandle;
	}

	public function getSecret()
	{
		return $this->secret;
	}

	public function getMacFunc()
	{
		return $this->macFunc;
	}

	/**
	 * Generates an association from an request
	 *
	 * @param PSX\OpenId\Provider\Data\AssociationRequest $request
	 * @return PSX\OpenId\Provider\Association
	 */
	public function generate(AssociationRequest $request)
	{
		// generate secret
		switch($request->getAssocType())
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
		switch($request->getSessionType())
		{
			case 'no-encryption':
				// $secret = base64_encode($secret);
				// $this->macKey = $secret;
				throw new InvalidDataException('no-encryption not supported');
				break;

			case 'DH-SHA1':
				$dh = ProviderAbstract::generateDh($request->getDhGen(), $request->getDhModulus(), $request->getDhConsumerPublic(), $macFunc, $secret);

				$this->dhServerPublic = $dh['pubKey'];
				$this->encMacKey      = $dh['macKey'];
				break;

			case 'DH-SHA256':
				$dh = ProviderAbstract::generateDh($request->getDhGen(), $request->getDhModulus(), $request->getDhConsumerPublic(), $macFunc, $secret);

				$this->dhServerPublic = $dh['pubKey'];
				$this->encMacKey      = $dh['macKey'];
				break;

			default:
				throw new InvalidDataException('Invalid association type');
				break;
		}

		$this->assocHandle = ProviderAbstract::generateHandle();
		$this->secret      = base64_encode($secret);
		$this->macFunc     = $macFunc;

		$assoc = new Association();
		$assoc->setAssocHandle($this->assocHandle);
		$assoc->setAssocType($request->getAssocType());
		$assoc->setSessionType($request->getSessionType());
		$assoc->setSecret($this->secret);

		return $assoc;
	}
}
