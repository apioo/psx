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
 * BasicAuthentication
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class BasicAuthentication implements FilterInterface
{
    protected $isValidCallback;
    protected $successCallback;
    protected $failureCallback;
    protected $missingCallback;

    /**
     * The isValidCallback is called with the provided username and password
     * if an Authorization header is present. Depending on the result the
     * onSuccess or onFailure callback is called. If the header is missing the
     * onMissing callback is called
     *
     * @param Closure $isValidCallback
     */
    public function __construct(Closure $isValidCallback)
    {
        $this->isValidCallback = $isValidCallback;

        $this->onSuccess(function () {
            // authentication successful
        });

        $this->onFailure(function () {
            throw new BadRequestException('Invalid username or password');
        });

        $this->onMissing(function (ResponseInterface $response) {
            $params = array(
                'realm' => 'psx',
            );

            throw new UnauthorizedException('Missing authorization header', 'Basic', $params);
        });
    }

    public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain)
    {
        $authorization = $request->getHeader('Authorization');

        if (!empty($authorization)) {
            $parts = explode(' ', $authorization, 2);
            $type  = isset($parts[0]) ? $parts[0] : null;
            $data  = isset($parts[1]) ? $parts[1] : null;

            if ($type == 'Basic' && !empty($data)) {
                $data  = base64_decode($data);
                $parts = explode(':', $data, 2);

                $username = isset($parts[0]) ? $parts[0] : null;
                $password = isset($parts[1]) ? $parts[1] : null;
                $result   = call_user_func_array($this->isValidCallback, array($username, $password));

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
