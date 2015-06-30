<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Oauth\Provider;

use PSX\Controller\ApiAbstract;
use PSX\Data\WriterInterface;
use PSX\Exception;
use PSX\Oauth;
use PSX\Oauth\Provider\Data\Consumer;
use PSX\Oauth\Provider\Data\Request;
use PSX\Oauth\Provider\Data\Response;

/**
 * RequestAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
		$extractor = new AuthorizationHeaderExtractor(array(
			'consumerKey',
			'signatureMethod',
			'signature',
			'timestamp',
			'nonce',
			'version',
			'callback',
		));

		$request  = $extractor->extract($this->request, new Request());
		$consumer = $this->getConsumer($request->getConsumerKey());

		if($consumer instanceof Consumer)
		{
			$signature = Oauth::getSignature($request->getSignatureMethod());

			$method = $this->request->getMethod();
			$url    = $this->request->getUri();
			$params = array_merge($request->getRecordInfo()->getData(), $this->request->getUri()->getParameters());

			$baseString = Oauth::buildBasestring($method, $url, $params);


			if($signature->verify($baseString, $consumer->getConsumerSecret(), '', $request->getSignature()) !== false)
			{
				$response = $this->getResponse($consumer, $request);

				if($response instanceof Response)
				{
					$response->addParam('oauth_callback_confirmed', true);

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
	 * @return \PSX\Oauth\Provider\Data\Consumer
	 */
	abstract protected function getConsumer($consumerKey);

	/**
	 * Returns the response depending on the $consumer and $request
	 *
	 * @param \PSX\Oauth\Provider\Data\Consumer $consumer
	 * @param \PSX\Oauth\Provider\Data\Request $request
	 * @return \PSX\Oauth\Provider\Data\Response
	 */
	abstract protected function getResponse(Consumer $consumer, Request $request);
}

