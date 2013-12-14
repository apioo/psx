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

namespace PSX\Oauth;

use PSX\Data\ReaderInterface;
use PSX\Exception;
use PSX\Module\ApiAbstract;
use PSX\Oauth;
use PSX\Oauth\Provider\Data\Request;
use PSX\Oauth\Provider\Data\Consumer;
use PSX\Url;

/**
 * This class is deprecated instead of extending this class add the method
 * getRequestFilter() in your module and return the filter
 * Dispatch\RequestFilter\OauthAuthentication in order to provide oauth 
 * authentication to your module
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ProviderAbstract extends ApiAbstract
{
	protected function handle()
	{
		$request  = new Request();
		$importer = new Data\RequestImporter();
		$importer->setRequiredFields(array(
			'consumerKey',
			'token',
			'signatureMethod',
			'signature',
			'timestamp',
			'nonce',
		));
		$importer->import($request, $this->getRequest(ReaderInterface::RAW));

		$consumer = $this->getConsumer($request->getConsumerKey(), $request->getToken());

		if($consumer instanceof Consumer)
		{
			$signature = Oauth::getSignature($request->getSignatureMethod());

			$method = $_SERVER['REQUEST_METHOD'];
			$url    = new Url($this->base->getSelf());
			$params = array_merge($request->getData(), $_GET);

			$baseString = Oauth::buildBasestring($method, $url, $params);


			if($signature->verify($baseString, $consumer->getConsumerSecret(), $consumer->getTokenSecret(), $request->getSignature()) !== false)
			{
				$this->onAuthenticated();
			}
			else
			{
				throw new Exception('Invalid signature');
			}
		}
		else
		{
			throw new Exception('Invalid Consumer Key');
		}
	}

	/**
	 * Returns the consumer object with the $consumerKey and $token
	 *
	 * @param string $consumerKey
	 * @param string $token
	 * @return PSX\Oauth\Provider\Data\Consumer
	 */
	abstract protected function getConsumer($consumerKey, $token);

	/**
	 * Is called if the authentication was verified
	 *
	 * @return void
	 */
	abstract protected function onAuthenticated();
}

