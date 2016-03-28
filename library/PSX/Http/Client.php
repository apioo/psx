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

namespace PSX\Http;

use InvalidArgumentException;
use PSX\Http\Handler\Curl;
use PSX\Uri\Uri;
use PSX\Uri\UriResolver;

/**
 * This class offers a simple way to make http requests. It can use either curl
 * or fsockopen handler to send the request. Here an example of an basic GET
 * request
 * <code>
 * $http     = new Client();
 * $request  = new GetRequest('http://google.com');
 * $response = $http->request($request);
 *
 * if($response->getStatusCode() == 200)
 * {
 *   echo (string) $response->getBody();
 * }
 * </code>
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Client implements ClientInterface
{
    /**
     * @var \PSX\Http\HandlerInterface
     */
    protected $handler;

    /**
     * @var \PSX\Http\CookieStoreInterface
     */
    protected $cookieStore;

    /**
     * If no handler is defined the curl handler is used as fallback
     *
     * @param \PSX\Http\HandlerInterface $handler
     */
    public function __construct(HandlerInterface $handler = null)
    {
        $this->handler = $handler !== null ? $handler : new Curl();
    }

    /**
     * Sends the request through the given handler and returns the response
     *
     * @param \PSX\Http\Request $request
     * @param \PSX\Http\Options $options
     * @param integer $count
     * @return \PSX\Http\Response
     */
    public function request(Request $request, Options $options = null, $count = 0)
    {
        if (!$request->getUri()->isAbsolute()) {
            throw new InvalidArgumentException('Request url must be absolute');
        }

        // set cookie headers
        if ($this->cookieStore !== null) {
            $cookies = $this->cookieStore->load($request->getUri()->getHost());

            if (!empty($cookies)) {
                $kv = array();

                foreach ($cookies as $cookie) {
                    $path = ltrim($cookie->getPath(), '/');

                    if ($cookie->getExpires() !== null && $cookie->getExpires()->getTimestamp() < time()) {
                        $this->cookieStore->remove($request->getUri()->getHost(), $cookie);
                    } elseif ($cookie->getPath() !== null && substr($request->getUri()->getPath(), 0, strlen($path)) != $path) {
                        // path does not fit
                    } else {
                        $kv[] = $cookie->getName() . '=' . $cookie->getValue();
                    }
                }

                $request->addHeader('Cookie', implode('; ', $kv));
            }
        }

        // set content length
        $body = $request->getBody();

        if ($body !== null && $request->hasHeader('Transfer-Encoding') != 'chunked' && !in_array($request->getMethod(), array('HEAD', 'GET'))) {
            $size = $body->getSize();

            if ($size !== false) {
                $request->setHeader('Content-Length', $size);
            }
        }

        // set default options
        if ($options === null) {
            $options = new Options();
        }

        // make request
        $response = $this->handler->request($request, $options);

        // store cookies
        if ($this->cookieStore !== null) {
            $cookies = $response->getHeaderLines('Set-Cookie');

            foreach ($cookies as $rawCookie) {
                try {
                    $cookie = new Cookie($rawCookie);
                    $domain = $cookie->getDomain() !== null ? $cookie->getDomain() : $request->getUri()->getHost();

                    $this->cookieStore->store($domain, $cookie);
                } catch (InvalidArgumentException $e) {
                    // invalid cookies
                }
            }
        }

        // check follow location
        if ($options->getFollowLocation() && ($response->getStatusCode() >= 300 && $response->getStatusCode() < 400)) {
            $location = (string) $response->getHeader('Location');

            if (!empty($location) && $location != $request->getUri()->toString()) {
                if ($options->getMaxRedirects() > $count) {
                    $location = UriResolver::resolve($request->getUri(), new Uri($location));

                    return $this->request(new GetRequest($location), $options, ++$count);
                } else {
                    throw new RedirectException('Max redirection reached');
                }
            }
        }

        return $response;
    }

    /**
     * Sets the handler
     *
     * @param \PSX\Http\HandlerInterface $handler
     * @return void
     */
    public function setHandler(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Returns the handler
     *
     * @return \PSX\Http\HandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Sets an cookie store
     *
     * @param \PSX\Http\CookieStoreInterface
     */
    public function setCookieStore(CookieStoreInterface $cookieStore)
    {
        $this->cookieStore = $cookieStore;
    }

    /**
     * Returns the cookie store
     *
     * @return \PSX\Http\CookieStoreInterface
     */
    public function getCookieStore()
    {
        return $this->cookieStore;
    }
}
