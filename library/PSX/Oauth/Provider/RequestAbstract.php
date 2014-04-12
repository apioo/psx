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

namespace PSX\Oauth\Provider;

use PSX\Exception;
use PSX\Controller\ApiAbstract;
use PSX\Oauth;
use PSX\Oauth\Provider\Data\Request;
use PSX\Oauth\Provider\Data\Response;
use PSX\Oauth\Provider\Data\Consumer;
use PSX\Data\ReaderInterface;
use PSX\Data\WriterInterface;
use PSX\Url;

/**
 * RequestAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class RequestAbstract extends ApiAbstract
{
	public function onGet()
	{
		throw new Exception('Invalid request method', 405);
	}

	public function onPost()
	{
		$this->doHandle();
	}

	protected function doHandle()
	{
		$request  = new Request();
		$importer = new Data\RequestImporter();
		$importer->setRequiredFields(array(
			'consumerKey',
			'signatureMethod',
			'signature',
			'timestamp',
			'nonce',
			'version',
			'callback',
		));
		$importer->import($request, $this->getBody(ReaderInterface::RAW));

		$consumer = $this->getConsumer($request->getConsumerKey());

		if($consumer instanceof Consumer)
		{
			$signature = Oauth::getSignature($request->getSignatureMethod());

			$method = $this->request->getMethod();
			$url    = $this->request->getUrl();
			$params = array_merge($request->getRecordInfo()->getData(), $this->request->getUrl()->getParams());

			$baseString = Oauth::buildBasestring($method, $url, $params);


			if($signature->verify($baseString, $consumer->getConsumerSecret(), '', $request->getSignature()) !== false)
			{
				$response = $this->getResponse($consumer, $request);

				if($response instanceof Response)
				{
					$response->addParam('oauth_callback_confirmed', true);

					$this->setResponse($response, WriterInterface::FORM);
				}
				else
				{
					throw new Exception('Invalid response');
				}
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
	 * @return PSX\Oauth\Provider\Data\Consumer
	 */
	abstract protected function getConsumer($consumerKey);

	/**
	 * Returns the response depending on the $consumer and $request
	 *
	 * @param PSX\Oauth\Provider\Data\Consumer $consumer
	 * @param PSX\Oauth\Provider\Data\Request $request
	 * @return PSX\Oauth\Provider\Data\Response
	 */
	abstract protected function getResponse(Consumer $consumer, Request $request);
}

