<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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
 * AccessAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class AccessAbstract extends ApiAbstract
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
		$extractor = new AuthorizationHeaderExtractor(array(
			'consumerKey',
			'token',
			'signatureMethod',
			'signature',
			'timestamp',
			'nonce',
			'verifier',
		));

		$request  = $extractor->extract($this->request, new Request());
		$consumer = $this->getConsumer($request->getConsumerKey(), $request->getToken());

		if($consumer instanceof Consumer)
		{
			$signature = Oauth::getSignature($request->getSignatureMethod());

			$method = $this->request->getMethod();
			$url    = $this->request->getUrl();
			$params = array_merge($request->getRecordInfo()->getData(), $this->request->getUrl()->getParameters());

			$baseString = Oauth::buildBasestring($method, $url, $params);


			if($signature->verify($baseString, $consumer->getConsumerSecret(), $consumer->getTokenSecret(), $request->getSignature()) !== false)
			{
				$response = $this->getResponse($consumer, $request);

				if($response instanceof Response)
				{
					$this->setBody($response, WriterInterface::FORM);
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
	 * @param string $token
	 * @return PSX\Oauth\Provider\Data\Consumer
	 */
	abstract protected function getConsumer($consumerKey, $token);

	/**
	 * Returns the response depending on the $consumer and $request
	 *
	 * @param PSX\Oauth\Provider\Data\Consumer $consumer
	 * @param PSX\Oauth\Provider\Data\Request $request
	 * @return PSX\Oauth\Provider\Data\Response
	 */
	abstract protected function getResponse(Consumer $consumer, Request $request);
}

