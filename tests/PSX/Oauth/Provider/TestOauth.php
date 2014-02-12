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

namespace PSX\Oauth\Provider;

use PSX\ControllerAbstract;
use PSX\Dispatch\Filter\OauthAuthentication;
use PSX\OauthTest;
use PSX\Oauth\Provider\Data\Response;
use PSX\Oauth\Provider\Data\Consumer;

/**
 * TestOauth
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TestOauth extends ControllerAbstract
{
	public function getRequestFilter()
	{
		$handle = new OauthAuthentication(function($consumerKey, $token){

			if($consumerKey == OauthTest::CONSUMER_KEY && $token == OauthTest::TOKEN)
			{
				return new Consumer(OauthTest::CONSUMER_KEY, OauthTest::CONSUMER_SECRET, OauthTest::TOKEN, OauthTest::TOKEN_SECRET);
			}

		});

		return array($handle);
	}

	/**
	 * @httpMethod GET
	 * @path /
	 */
	public function doIndex()
	{
		$this->response->setStatusCode(200);
		$this->response->getBody()->write('SUCCESS');
	}
}
