<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

use PSX\Framework\Controller\ApiAbstract;
use PSX\Data\WriterInterface;
use PSX\Oauth\Consumer;
use PSX\Oauth\Provider\Data\Credentials;
use PSX\Oauth\Provider\Data\Request;
use PSX\Oauth\Provider\Data\Response;
use PSX\Http\Exception as StatusCode;

/**
 * RequestAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class RequestAbstract extends ApiAbstract
{
    public function onLoad()
    {
        parent::onLoad();

        if ($this->request->getMethod() != 'POST') {
            throw new StatusCode\MethodNotAllowedException('Only POST requests are allowed', ['POST']);
        }
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

        if ($consumer instanceof Credentials) {
            $signature = Consumer::getSignature($request->getSignatureMethod());

            $method = $this->request->getMethod();
            $url    = $this->request->getUri();
            $params = array_merge($request->getProperties(), $this->request->getUri()->getParameters());

            $baseString = Consumer::buildBasestring($method, $url, $params);

            if ($signature->verify($baseString, $consumer->getConsumerSecret(), '', $request->getSignature()) !== false) {
                $response = $this->getResponse($consumer, $request);

                if ($response instanceof Response) {
                    $response->addParam('oauth_callback_confirmed', true);

                    $this->setBody($response);
                } else {
                    throw new StatusCode\BadRequestException('Invalid response');
                }
            } else {
                throw new StatusCode\BadRequestException('Invalid signature');
            }
        } else {
            throw new StatusCode\BadRequestException('Invalid Consumer Key');
        }
    }

    protected function getSupportedWriter()
    {
        return [WriterInterface::FORM];
    }

    /**
     * Returns the consumer object with the $consumerKey and $token
     *
     * @param string $consumerKey
     * @return \PSX\Oauth\Provider\Data\Credentials
     */
    abstract protected function getConsumer($consumerKey);

    /**
     * Returns the response depending on the $credentials and $request
     *
     * @param \PSX\Oauth\Provider\Data\Credentials $credentials
     * @param \PSX\Oauth\Provider\Data\Request $request
     * @return \PSX\Oauth\Provider\Data\Response
     */
    abstract protected function getResponse(Credentials $credentials, Request $request);
}
