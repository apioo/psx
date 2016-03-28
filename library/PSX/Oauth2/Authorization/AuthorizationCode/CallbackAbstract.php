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

namespace PSX\Oauth2\Authorization\AuthorizationCode;

use PSX\Framework\Controller\ApiAbstract;
use RuntimeException;
use PSX\Oauth2\AccessToken;
use PSX\Oauth2\Authorization\Exception\ErrorExceptionAbstract;
use PSX\Oauth2\AuthorizationAbstract;

/**
 * CallbackAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class CallbackAbstract extends ApiAbstract
{
    public function onLoad()
    {
        parent::onLoad();

        try {
            $error = $this->getParameter('error');

            // error detection
            if (!empty($error)) {
                AuthorizationAbstract::throwErrorException($this->request->getUri()->getParameters());
            }

            $code  = $this->getParameter('code');
            $state = $this->getParameter('state');

            if (empty($code)) {
                throw new RuntimeException('Code not available');
            }

            $redirectUri = '';

            // get access token
            $accessToken = $this->getAuthorizationCode($code, $state)->getAccessToken($code, $redirectUri);

            $this->onAccessToken($accessToken);
        } catch (ErrorExceptionAbstract $e) {
            $this->onError($e);
        }
    }

    /**
     * Should return the authorization code object containing the endpoint url
     * and the client id and secret
     *
     * @param string $code
     * @param string $state
     * @return \PSX\Oauth2\Authorization\AuthorizationCode
     */
    abstract protected function getAuthorizationCode($code, $state);

    /**
     * Is called if we have obtained an access token from the authorization
     * server
     *
     * @param \PSX\Oauth2\AccessToken $accessToken
     */
    abstract protected function onAccessToken(AccessToken $accessToken);

    /**
     * Is called if the client was redirected with an GET error parameter
     *
     * @param \Exception $e
     */
    abstract protected function onError(\Exception $e);
}
