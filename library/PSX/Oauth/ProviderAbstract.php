<?php
/*
 *  $Id: ProviderAbstract.php 506 2012-06-03 13:44:51Z k42b3.x@googlemail.com $
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
 * PSX_Oauth_ProviderAbstract
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Oauth
 * @version    $Revision: 506 $
 */
abstract class PSX_Oauth_ProviderAbstract extends PSX_Module_ApiAbstract
{
	protected function handle()
	{
		$request = new PSX_Oauth_Provider_Data_Request();

		$request->setRequiredFields(array(

			'consumerKey',
			'token',
			'signatureMethod',
			'signature',
			'timestamp',
			'nonce',

		));

		$request->import($this->getRequest(PSX_Data_ReaderInterface::RAW));


		$consumer = $this->getConsumer($request->getConsumerKey(), $request->getToken());

		if(is_object($consumer) && $consumer instanceof PSX_Oauth_Provider_Data_Consumer)
		{
			$signature = PSX_Oauth::getSignature($request->getSignatureMethod());

			$method = $_SERVER['REQUEST_METHOD'];
			$url    = new PSX_Url($this->base->getSelf());
			$params = array_merge($request->getData(), $_GET);

			$baseString = PSX_Oauth::buildBasestring($method, $url, $params);


			if($signature->verify($baseString, $consumer->getConsumerSecret(), $consumer->getTokenSecret(), $request->getSignature()) !== false)
			{
				$this->onAuthenticated();
			}
			else
			{
				throw new PSX_Oauth_Provider_Exception('Invalid signature');
			}
		}
		else
		{
			throw new PSX_Oauth_Provider_Exception('Invalid Consumer Key');
		}
	}

	/**
	 * Returns the consumer object with the $consumerKey and $token
	 *
	 * @param string $consumerKey
	 * @param string $token
	 * @return PSX_Oauth_Provider_Data_Consumer
	 */
	abstract protected function getConsumer($consumerKey, $token);

	/**
	 * Is called if the authentication was verified
	 *
	 * @return void
	 */
	abstract protected function onAuthenticated();
}

