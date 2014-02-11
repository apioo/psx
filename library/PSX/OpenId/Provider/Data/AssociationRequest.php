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
use PSX\Data\RecordInfo;
use PSX\Data\ReaderInterface;
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
	protected $assocType;
	protected $sessionType;
	protected $dhModulus;
	protected $dhGen;
	protected $dhConsumerPublic;

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

	public function getAssocType()
	{
		return $this->assocType;
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

	public function getSessionType()
	{
		return $this->sessionType;
	}

	public function setDhModulus($modulus)
	{
		$this->dhModulus = $modulus;
	}

	public function getDhModulus()
	{
		return $this->dhModulus;
	}

	public function setDhGen($gen)
	{
		$this->dhGen = $gen;
	}

	public function getDhGen()
	{
		return $this->dhGen;
	}

	public function setDhConsumerPublic($consumerPublic)
	{
		$this->dhConsumerPublic = $consumerPublic;
	}

	public function getDhConsumerPublic()
	{
		return $this->dhConsumerPublic;
	}
}
