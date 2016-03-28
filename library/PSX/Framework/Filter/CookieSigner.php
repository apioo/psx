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

use PSX\Framework\Exception;
use PSX\Http\Cookie;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Json\Parser;

/**
 * The cookie signer adds a signature to the cookie so that you can verify that
 * the value which was set by the server has not changed. Note it does not
 * encrypt the values so everybody can still read the data like in a normal
 * cookie but you can verify that the data was not modified
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CookieSigner implements FilterInterface
{
    const COOKIE_NAME = 'psx_cookie';

    protected $secretKey;

    public function __construct($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain)
    {
        $signature = null;

        if ($request->hasHeader('Cookie')) {
            $cookies = Cookie::parseList($request->getHeader('Cookie'));

            foreach ($cookies as $cookie) {
                if ($cookie->getName() == self::COOKIE_NAME) {
                    $data      = $cookie->getValue();
                    $parts     = explode('.', $data, 2);

                    $payload   = isset($parts[0]) ? $parts[0] : null;
                    $signature = isset($parts[1]) ? $parts[1] : null;

                    if (strcmp($signature, $this->generateSignature($payload)) === 0) {
                        $request->setAttribute(self::COOKIE_NAME, $this->unserializeData($payload));
                    } else {
                        // invalid signature
                    }

                    break;
                }
            }
        }

        $filterChain->handle($request, $response);

        $data = $request->getAttribute(self::COOKIE_NAME);

        if (!empty($data)) {
            $payload      = $this->serializeData($data);
            $newSignature = $this->generateSignature($payload);

            // send only a new cookie if the data has changed
            if ($newSignature != $signature) {
                $response->addHeader('Set-Cookie', self::COOKIE_NAME . '=' . $payload . '.' . $newSignature);
            }
        }
    }

    protected function generateSignature($data)
    {
        return base64_encode(hash_hmac('sha256', $data, $this->secretKey, true));
    }

    protected function unserializeData($data)
    {
        try {
            return Parser::decode(base64_decode($data), true);
        } catch (Exception $e) {
            return null;
        }
    }

    protected function serializeData($data)
    {
        return base64_encode(Parser::encode($data));
    }
}
