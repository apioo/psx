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

namespace PSX\Payment\Paypal;

/**
 * Credentials
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Credentials
{
	protected $endpoint;
	protected $clientId;
	protected $clientSecret;
	protected $certificate;

	public function __construct($endpoint, $clientId, $clientSecret, $certificate)
	{
		$this->endpoint     = $endpoint;
		$this->clientId     = $clientId;
		$this->clientSecret = $clientSecret;
		$this->certificate  = $certificate;
	}

	public function setEndpoint($endpoint)
	{
		$this->endpoint = $endpoint;
	}
	
	public function getEndpoint()
	{
		return $this->endpoint;
	}

	public function setClientId($clientId)
	{
		$this->clientId = $clientId;
	}
	
	public function getClientId()
	{
		return $this->clientId;
	}

	public function setClientSecret($clientSecret)
	{
		$this->clientSecret = $clientSecret;
	}
	
	public function getClientSecret()
	{
		return $this->clientSecret;
	}

	public function setCertificate($certificate)
	{
		$this->certificate = $certificate;
	}
	
	public function getCertificate()
	{
		return $this->certificate;
	}
}
