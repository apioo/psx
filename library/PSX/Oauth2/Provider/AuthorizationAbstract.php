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

namespace PSX\Oauth2\Provider;

use PSX\Controller\ApiAbstract;
use PSX\Oauth2\Authorization\Exception\ErrorExceptionAbstract;
use PSX\Oauth2\Authorization\Exception\InvalidRequestException;
use PSX\Oauth2\Authorization\Exception\ServerErrorException;
use PSX\Oauth2\Authorization\Exception\UnauthorizedClientException;
use PSX\Oauth2\Authorization\Exception\UnsupportedResponseTypeException;
use PSX\Url;

/**
 * AuthorizationAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class AuthorizationAbstract extends ApiAbstract
{
    /**
     * @Inject oauth2_grant_type_factory
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
        $responseType = $this->getParameter('response_type');
        $clientId     = $this->getParameter('client_id');
        $redirectUri  = $this->getParameter('redirect_uri');
        $scope        = $this->getParameter('scope');
        $state        = $this->getParameter('state');

        try {
            $request = new AccessRequest($clientId, $redirectUri, $scope, $state);

            if (empty($responseType) || empty($clientId) || empty($state)) {
                throw new InvalidRequestException('Missing parameters');
            }

            if (!$this->hasGrant($request)) {
                throw new UnauthorizedClientException('Client is not authenticated');
            }

            switch ($responseType) {
                case 'code':
                    $this->handleCode($request);
                    break;

                case 'token':
                    $this->handleToken($request);
                    break;

                default:
                    throw new UnsupportedResponseTypeException('Invalid response type');
                    break;
            }
        } catch (ErrorExceptionAbstract $e) {
            if (!empty($redirectUri)) {
                $redirectUri = new Url($redirectUri);

                $parameters = $redirectUri->getParameters();
                $parameters['error'] = $e->getType();
                $parameters['error_description'] = $e->getMessage();

                $this->redirect($redirectUri->withParameters($parameters)->toString());
            } else {
                throw $e;
            }
        }
    }

    protected function handleCode(AccessRequest $request)
    {
        $url = $this->getRedirectUri($request);

        if ($url instanceof Url) {
            $parameters = $url->getParameters();
            $parameters['code'] = $this->generateCode($request);

            if ($request->hasState()) {
                $parameters['state'] = $request->getState();
            }

            $this->redirect($url->withParameters($parameters)->toString());
        } else {
            throw new ServerErrorException('No redirect uri available');
        }
    }

    protected function handleToken(AccessRequest $request)
    {
        $url = $this->getRedirectUri($request);

        if ($url instanceof Url) {
            // we must create an access token and append it to the redirect_uri
            // fragment or display an redirect form
            $accessToken = $this->grantTypeFactory->get(GrantTypeInterface::TYPE_IMPLICIT)->generateAccessToken(null, array(
                'scope' => $request->getScope()
            ));

            $fields = array(
                'access_token' => $accessToken->getAccessToken(),
                'token_type'   => $accessToken->getTokenType(),
            );

            if ($request->hasState()) {
                $fields['state'] = $request->getState();
            }

            $this->redirect($url->withFragment(http_build_query($fields, '', '&'))->toString());
        } else {
            throw new ServerErrorException('No redirect uri available');
        }
    }

    protected function getRedirectUri(AccessRequest $request)
    {
        if ($request->hasRedirectUri()) {
            return $request->getRedirectUri();
        } else {
            return $this->getCallback($request->getClientId());
        }
    }

    /**
     * This method is called if no redirect_uri was set you can overwrite this
     * method if its possible to get an callback from another source
     *
     * @param string $clientId
     * @return \PSX\Url
     */
    protected function getCallback($clientId)
    {
        return null;
    }

    /**
     * Returns whether the user has authorized the client_id. This method must
     * redirect the user to an login form and display an form where the user can
     * grant the authorization request
     *
     * @param \PSX\Oauth2\Provider\AccessRequest $request
     * @return boolean
     */
    abstract protected function hasGrant(AccessRequest $request);

    /**
     * Generates an authorization code which is assigned to the request
     *
     * @param \PSX\Oauth2\Provider\AccessRequest $request
     * @return string
     */
    abstract protected function generateCode(AccessRequest $request);
}
