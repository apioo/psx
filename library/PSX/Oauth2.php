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

namespace PSX;

use PSX\Oauth2\AccessToken;
use PSX\Oauth2\TokenAbstract;

/**
 * Oauth2 client implementation. Here an example howto access an oauth2
 * protected api
 * <code>
 * // redirect the customer to the auth url of the provider
 * AuthorizationCode::redirect('[auth_url]', '[client_id]', '[redirect_url]');
 *
 * // if the customer returns get an access token
 * $http = new Http();
 * $code = new AuthorizationCode($http, new Url('[token_url]'));
 * $code->setClientPassword('[client_id]', '[client_secret]', AuthorizationCode::AUTH_POST);
 *
 * $accessToken = $code->getAccessToken('[redirect_url]');
 *
 * // if we have an access token we can request the api
 * $oauth    = new Oauth2($http);
 * $header   = array(
 * 	'Authorization' => $oauth->getAuthorizationHeader($accessToken)
 * );
 * $request  = new GetRequest('[api_url]', $header);
 * $response = $http->request($request);
 *
 * if($response->getStatusCode() == 200)
 * {
 * 	// request worked
 * }
 * </code>
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     http://tools.ietf.org/html/rfc5849
 */
class Oauth2
{
    /**
     * If you have received an access token you can use this method to get the
     * authorization header. You can add the header to an http request to make
     * an valid oauth2 request i.e.
     * <code>
     * $header = array(
     * 	'Authorization: ' . $oauth->getAuthorizationHeader(...),
     * );
     * </code>
     *
     * @param \PSX\Oauth2\AccessToken $accessToken
     * @return string
     */
    public function getAuthorizationHeader(AccessToken $accessToken)
    {
        return TokenAbstract::factory($accessToken)->getHeader();
    }
}
