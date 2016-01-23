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

namespace PSX\Oauth2\Provider;

use PSX\Controller\ApiAbstract;
use PSX\Data\ReaderInterface;
use PSX\Data\WriterInterface;
use PSX\Oauth2\Authorization\Exception\ErrorExceptionAbstract;

/**
 * TokenAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class TokenAbstract extends ApiAbstract
{
    /**
     * @Inject("oauth2_grant_type_factory")
     * @var \PSX\Oauth2\Provider\GrantTypeFactory
     */
    protected $grantTypeFactory;

    public function onGet()
    {
        $this->doHandle();
    }

    public function onPost()
    {
        $this->doHandle();
    }

    protected function doHandle()
    {
        $parameters  = (array) $this->getBody(ReaderInterface::FORM);
        $grantType   = isset($parameters['grant_type']) ? $parameters['grant_type'] : null;
        $scope       = isset($parameters['scope']) ? $parameters['scope'] : null;
        $credentials = null;

        $auth  = $this->request->getHeader('Authorization');
        $parts = explode(' ', $auth, 2);
        $type  = isset($parts[0]) ? $parts[0] : null;
        $data  = isset($parts[1]) ? $parts[1] : null;

        if ($type == 'Basic' && !empty($data)) {
            $data         = explode(':', base64_decode($data), 2);
            $clientId     = isset($data[0]) ? $data[0] : null;
            $clientSecret = isset($data[1]) ? $data[1] : null;

            if (!empty($clientId) && !empty($clientSecret)) {
                $credentials = new Credentials($clientId, $clientSecret);
            }
        }

        if ($credentials === null && isset($parameters['client_id']) && isset($parameters['client_secret'])) {
            $credentials = new Credentials($parameters['client_id'], $parameters['client_secret']);
        }

        try {
            // we get the grant type factory from the DI container the factory
            // contains the available grant types
            $accessToken = $this->grantTypeFactory->get($grantType)->generateAccessToken($credentials, $parameters);

            $this->response->setStatus(200);

            $this->setBody($accessToken, WriterInterface::JSON);
        } catch (ErrorExceptionAbstract $e) {
            $error = new Error();
            $error->setError($e->getType());
            $error->setErrorDescription($e->getMessage());
            $error->setState(null);

            $this->response->setStatus(400);

            $this->setBody($error, WriterInterface::JSON);
        } catch (\Exception $e) {
            $error = new Error();
            $error->setError('server_error');
            $error->setErrorDescription($e->getMessage());
            $error->setState(null);

            $this->response->setStatus(400);

            $this->setBody($error, WriterInterface::JSON);
        }
    }
}
