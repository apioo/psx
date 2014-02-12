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

namespace PSX\OpenId;

use PSX\OpenId\Provider\Association;
use PSX\OpenId\Provider\Redirect;
use PSX\OpenId\Provider\Data\ResRequest;
use PSX\OpenId\Provider\Data\SetupRequest;
use PSX\Url;

/**
 * TestProviderAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TestProviderAbstract extends ProviderAbstract
{
	protected static $assoc;

	public function onAsocciation(Association $assoc)
	{
		// the provider has to persist the association
		self::$assoc = $assoc;
	}

	public function onCheckidSetup(SetupRequest $request)
	{
		// the provider has to authenticate the user and if the authentication
		// was successful we can redirect the user back to the relying party. 
		// The url can contains extensions like sreg or ax
		$redirect = new Redirect();
		$redirect->setOpEndpoint('http://127.0.0.1/openid');
		$redirect->setClaimedId('http://k42b3.com');
		$redirect->setIdentity('http://k42b3.com');
		$redirect->setReturnTo('http://127.0.0.1/callback');
		$redirect->setResponseNonce(uniqid());
		$redirect->setAssocHandle(self::$assoc->getAssocHandle());
		$redirect->setParams(array('foo' => 'bar'));

		return $redirect->getUrl(self::$assoc->getSecret(), self::$assoc->getAssocType());
	}

	public function onCheckAuthentication(ResRequest $request)
	{
		// the request must contains all parameters from the redirect. We have
		// to check whether everything is valid and return true

		return $request->isValidSignature(self::$assoc->getSecret(), self::$assoc->getAssocType());
	}
}
