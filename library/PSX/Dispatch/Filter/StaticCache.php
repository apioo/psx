<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Dispatch\Filter;

use PSX\Cache\CacheItemPoolInterface;
use PSX\DateTime;
use PSX\Dispatch\FilterChainInterface;
use PSX\Dispatch\FilterInterface;
use PSX\Http\Exception\BadRequestException;
use PSX\Http\Stream\Util;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * StaticCache
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class StaticCache implements FilterInterface
{
	protected $cache;
	protected $keyGenerator;
	protected $ttl;

	/**
	 * @param PSX\Cache\CacheItemPoolInterface $cache
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

		if(!empty($key))
		{
			$item = $this->cache->getItem($key);

			if($item->isHit())
			{
				// serve cache response
				$resp = $item->get();

				$response->setHeaders($resp['headers']);
				$response->getBody()->write($resp['body']);
			}
			else
			{
				$filterChain->handle($request, $response);

				// save response
				$resp = array(
					'headers' => $this->getCacheHeaders($response),
					'body'    => Util::toString($response->getBody()),
				);

				$item->set($resp, $this->ttl);

				$this->cache->save($item);
			}
		}
		else
		{
			// if we have no key we can not use a cache
			$filterChain->handle($request, $response);
		}
	}

	protected function getCacheKey(RequestInterface $request)
	{
		if($request->getMethod() == 'GET')
		{
			if($this->keyGenerator === null)
			{
				return $this->getKeyDefaultImpl($request);
			}
			else
			{
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
	 * @param PSX\Http\RequestInterface $request
	 * @return string
	 */
	protected function getKeyDefaultImpl(RequestInterface $request)
	{
		$url      = $request->getUri();
		$query    = $url->getQuery();
		$fragment = $url->getFragment();

		if(empty($query) && empty($fragment))
		{
			// we cache the request only if we have no query or fragment values
			return md5($url->getPath());
		}

		return null;
	}

	/**
	 * Returns an array containing all headers which gets saved in the cache
	 *
	 * @return array
	 */
	protected function getCacheHeaders(ResponseInterface $response)
	{
		$headers = array(
			'Last-Modified' => date(DateTime::HTTP),
		);

		if($response->hasHeader('Content-Type'))
		{
			$headers['Content-Type'] = $response->getHeader('Content-Type');
		}

		return $headers;
	}
}
