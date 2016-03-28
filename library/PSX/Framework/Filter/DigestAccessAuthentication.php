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
use PSX\Data\Record\StoreInterface;
use PSX\Framework\Filter\DigestAccessAuthentication\Digest;
use PSX\Framework\Exception;
use PSX\Http\Authentication;
use PSX\Http\Exception\BadRequestException;
use PSX\Http\Exception\UnauthorizedException;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * Implementation of the http digest authentication. Note the digest
 * authentication has the advantage that the password is not transported in
 * plaintext over the wire instead a hash is used. This has the downside that we
 * need to rebuild and compare the hash on the server side and therefor we need
 * the password as plain text (wich requires you to store the password as
 * plaintext) or in exactly the hash format wich is used by the digest function
 * "md5([username]:[realm]:[pw])" wich is probably not often the case. If you
 * need www-authentication you probably should use https + basic authentication
 * since you are not required to store the password in such a format
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DigestAccessAuthentication implements FilterInterface
{
    protected $ha1Callback;
    protected $digestStore;
    protected $digest;

    protected $successCallback;
    protected $failureCallback;
    protected $missingCallback;

    /**
     * The ha1Callback should return "md5([username]:[realm]:[pw])" wich is then
     * used to compare the response from the client. If its successful the
     * onSuccess callback is called else the onFailure. If the Authorization
     * header is missing the onMissing callback is called. The default behavior
     * is to store the nonce and opaque in the session but you can overwrite
     * that by providing a nonce and opaque in the constructor and overwrite the
     * default onMissing callback
     *
     * @param Closure $ha1Callback
     * @param \PSX\Data\Record\StoreInterface $digestStore
     */
    public function __construct(Closure $ha1Callback, StoreInterface $digestStore)
    {
        $this->ha1Callback = $ha1Callback;
        $this->digestStore = $digestStore;

        $this->onSuccess(function () {
            // authentication successful
        });

        $this->onFailure(function () {
            throw new BadRequestException('Invalid username or password');
        });

        $this->onMissing(function (ResponseInterface $response) use ($digestStore) {
            $digest = new Digest();
            $digest->setNonce(sha1(time() . uniqid()));
            $digest->setOpaque(sha1(session_id()));

            $digestStore->save('digest', $digest);

            $params = array(
                'realm'  => 'psx',
                'qop'    => 'auth,auth-int',
                'nonce'  => $digest->getNonce(),
                'opaque' => $digest->getOpaque(),
            );

            throw new UnauthorizedException('Missing authorization header', 'Digest', $params);
        });

        // load digest from store
        $this->loadDigest();
    }

    public function loadDigest()
    {
        $this->digest = $this->digestStore->load('digest');
    }

    public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain)
    {
        $authorization = $request->getHeader('Authorization');

        if (!empty($authorization)) {
            $parts = explode(' ', $authorization, 2);
            $type  = isset($parts[0]) ? $parts[0] : null;
            $data  = isset($parts[1]) ? $parts[1] : null;

            if ($type == 'Digest' && !empty($data)) {
                $params = Authentication::decodeParameters($data);
                $algo   = isset($params['algorithm']) ? $params['algorithm'] : 'MD5';
                $qop    = isset($params['qop']) ? $params['qop'] : 'auth';

                if (!$this->digest instanceof Digest) {
                    throw new BadRequestException('Digest not available');
                }

                if ($this->digest->getOpaque() != $params['opaque']) {
                    throw new BadRequestException('Invalid opaque');
                }

                // build ha1
                $ha1 = call_user_func_array($this->ha1Callback, array($params['username']));

                if ($algo == 'MD5-sess') {
                    $ha1 = md5($ha1 . ':' . $this->digest->getNonce() . ':' . $params['cnonce']);
                }

                // build ha2
                if ($qop == 'auth-int') {
                    $ha2 = md5($request->getMethod() . ':' . $request->getUri()->getPath() . ':' . md5($request->getBody()));
                } else {
                    $ha2 = md5($request->getMethod() . ':' . $request->getUri()->getPath());
                }

                // build response
                if ($qop == 'auth' || $qop == 'auth-int') {
                    $hash = md5($ha1 . ':' . $this->digest->getNonce() . ':' . $params['nc'] . ':' . $params['cnonce'] . ':' . $qop . ':' . $ha2);
                } else {
                    $hash = md5($ha1 . ':' . $this->digest->getNonce() . ':' . $ha2);
                }

                if (strcmp($hash, $params['response']) === 0) {
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
