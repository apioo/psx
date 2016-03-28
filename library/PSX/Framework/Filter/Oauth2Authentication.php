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
use PSX\Http\Exception\BadRequestException;
use PSX\Http\Exception\UnauthorizedException;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * Oauth2Authentication
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Oauth2Authentication implements FilterInterface
{
    protected $accessCallback;
    protected $successCallback;
    protected $failureCallback;
    protected $missingCallback;

    /**
     * The accessCallback is called with the provided access token. At the
     * moment this class supports only Bearer authentication. If the
     * accessCallback explicit return true the authorization was successful
     *
     * @param Closure $accessCallback
     */
    public function __construct(Closure $accessCallback)
    {
        $this->accessCallback = $accessCallback;

        $this->onSuccess(function () {
            // authentication successful
        });

        $this->onFailure(function () {
            throw new BadRequestException('Invalid access token');
        });

        $this->onMissing(function (ResponseInterface $response) {
            $params = array(
                'realm' => 'psx',
            );

            throw new UnauthorizedException('Missing authorization header', 'Bearer', $params);
        });
    }

    public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain)
    {
        $authorization = $request->getHeader('Authorization');

        if (!empty($authorization)) {
            $parts       = explode(' ', $authorization, 2);
            $type        = isset($parts[0]) ? $parts[0] : null;
            $accessToken = isset($parts[1]) ? $parts[1] : null;

            if ($type == 'Bearer' && !empty($accessToken)) {
                $result = call_user_func_array($this->accessCallback, array($accessToken));

                if ($result === true) {
                    $this->callSuccess($response);

                    $filterChain->handle($request, $response);
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
