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

namespace PSX\Framework\Filter;

use Closure;
use PSX\Http\Authentication;
use PSX\Http\Exception\BadRequestException;
use PSX\Http\Exception\UnauthorizedException;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Oauth\Consumer;
use PSX\Oauth\Provider\Data\Credentials;

/**
 * OauthAuthentication
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class OauthAuthentication implements FilterInterface
{
    protected $consumerCallback;
    protected $successCallback;
    protected $failureCallback;
    protected $missingCallback;

    /**
     * The consumerCallback is called with the given consumerKey and token. The
     * callback should return an PSX\Oauth\Provider\Data\Consumer. If the
     * signature is valid the onSuccess else the onFailure callback is called.
     * If the Authorization header is missing the onMissing callback is called
     *
     * @param Closure $consumerCallback
     */
    public function __construct(Closure $consumerCallback)
    {
        $this->consumerCallback = $consumerCallback;

        $this->onSuccess(function () {
            // authentication successful
        });

        $this->onFailure(function () {
            throw new BadRequestException('Invalid consumer key or signature');
        });

        $this->onMissing(function (ResponseInterface $response) {
            $params = array(
                'realm' => 'psx',
            );

            throw new UnauthorizedException('Missing authorization header', 'Oauth', $params);
        });
    }

    public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain)
    {
        $authorization = $request->getHeader('Authorization');

        if (!empty($authorization)) {
            $parts = explode(' ', $authorization, 2);
            $type  = isset($parts[0]) ? $parts[0] : null;
            $data  = isset($parts[1]) ? $parts[1] : null;

            if ($type == 'OAuth' && !empty($data)) {
                $params = Authentication::decodeParameters($data);
                $params = array_map(array('\PSX\Oauth\Consumer', 'urlDecode'), $params);

                // realm is not used in the base string
                unset($params['realm']);

                if (!isset($params['oauth_consumer_key'])) {
                    throw new BadRequestException('Consumer key not set');
                }

                if (!isset($params['oauth_token'])) {
                    throw new BadRequestException('Token not set');
                }

                if (!isset($params['oauth_signature_method'])) {
                    throw new BadRequestException('Signature method not set');
                }

                if (!isset($params['oauth_signature'])) {
                    throw new BadRequestException('Signature not set');
                }

                $credentials = call_user_func_array($this->consumerCallback, array($params['oauth_consumer_key'], $params['oauth_token']));

                if ($credentials instanceof Credentials) {
                    $signature = Consumer::getSignature($params['oauth_signature_method']);

                    $method = $request->getMethod();
                    $url    = $request->getUri();
                    $params = array_merge($params, $request->getUri()->getParameters());

                    if (strpos($request->getHeader('Content-Type'), 'application/x-www-form-urlencoded') !== false) {
                        $body = (string) $request->getBody();
                        $data = array();

                        parse_str($body, $data);

                        $params = array_merge($params, $data);
                    }

                    $baseString = Consumer::buildBasestring($method, $url, $params);

                    if ($signature->verify($baseString, $credentials->getConsumerSecret(), $credentials->getTokenSecret(), $params['oauth_signature']) !== false) {
                        $this->callSuccess($response);

                        $filterChain->handle($request, $response);
                    } else {
                        $this->callFailure($response);
                    }
                } else {
                    $this->callFailure($response);
                }
            } else {
                $this->callMissing($response);
            }
        } else {
            $this->callMissing($response);
        }
    }

    public function onSuccess(Closure $successCallback)
    {
        $this->successCallback = $successCallback;
    }

    public function onFailure(Closure $failureCallback)
    {
        $this->failureCallback = $failureCallback;
    }

    public function onMissing(Closure $missingCallback)
    {
        $this->missingCallback = $missingCallback;
    }

    protected function callSuccess(ResponseInterface $response)
    {
        call_user_func_array($this->successCallback, array($response));
    }

    protected function callFailure(ResponseInterface $response)
    {
        call_user_func_array($this->failureCallback, array($response));
    }

    protected function callMissing(ResponseInterface $response)
    {
        call_user_func_array($this->missingCallback, array($response));
    }
}
