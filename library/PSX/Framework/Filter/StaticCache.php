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

use Psr\Cache\CacheItemPoolInterface;
use PSX\DateTime\DateTime;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Http\Stream\Util;

/**
 * StaticCache
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class StaticCache implements FilterInterface
{
    protected $cache;
    protected $keyGenerator;
    protected $ttl;

    /**
     * @param \Psr\Cache\CacheItemPoolInterface $cache
     * @param callable $keyGenerator
     * @param integer $ttl
     */
    public function __construct(CacheItemPoolInterface $cache, $keyGenerator = null, $ttl = null)
    {
        $this->cache        = $cache;
        $this->keyGenerator = $keyGenerator;
        $this->ttl          = $ttl;
    }

    public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain)
    {
        $key = $this->getCacheKey($request);

        if (!empty($key)) {
            $item = $this->cache->getItem($key);

            if ($item->isHit()) {
                // serve cache response
                $resp = $item->get();

                $response->setHeaders($resp['headers']);
                $response->getBody()->write($resp['body']);
            } else {
                $filterChain->handle($request, $response);

                // save response
                $resp = array(
                    'headers' => $this->getCacheHeaders($response),
                    'body'    => Util::toString($response->getBody()),
                );

                $item->set($resp, $this->ttl);

                $this->cache->save($item);
            }
        } else {
            // if we have no key we can not use a cache
            $filterChain->handle($request, $response);
        }
    }

    protected function getCacheKey(RequestInterface $request)
    {
        if ($request->getMethod() == 'GET') {
            if ($this->keyGenerator === null) {
                return $this->getKeyDefaultImpl($request);
            } else {
                return call_user_func_array($this->keyGenerator, array($request));
            }
        }

        return null;
    }

    /**
     * Returns an string which gets used by the cache as key. You can provide a
     * custom key generator function in the constructor to override this
     * behaviour
     *
     * @param \PSX\Http\RequestInterface $request
     * @return string
     */
    protected function getKeyDefaultImpl(RequestInterface $request)
    {
        $url      = $request->getUri();
        $query    = $url->getQuery();
        $fragment = $url->getFragment();

        if (empty($query) && empty($fragment)) {
            // we cache the request only if we have no query or fragment values
            return md5($url->getPath());
        }

        return null;
    }

    /**
     * Returns an array containing all headers which gets saved in the cache
     *
     * @param \PSX\Http\ResponseInterface $response
     * @return array
     */
    protected function getCacheHeaders(ResponseInterface $response)
    {
        $headers = array(
            'Last-Modified' => date(DateTime::HTTP),
        );

        if ($response->hasHeader('Content-Type')) {
            $headers['Content-Type'] = $response->getHeader('Content-Type');
        }

        return $headers;
    }
}
