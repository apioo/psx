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

use PSX\DateTime\DateTime;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * Uses http headers to controle the browser cache of the client
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class BrowserCache implements FilterInterface
{
    const TYPE_PUBLIC      = 0x1;
    const TYPE_PRIVATE     = 0x2;
    const NO_CACHE         = 0x4;
    const NO_STORE         = 0x8;
    const NO_TRANSFORM     = 0x10;
    const MUST_REVALIDATE  = 0x20;
    const PROXY_REVALIDATE = 0x40;

    protected $flags;
    protected $maxAge;
    protected $sMaxAge;
    protected $expires;

    public function __construct($flags = 0, $maxAge = null, $sMaxAge = null, \DateTime $expires = null)
    {
        $this->flags   = $flags;
        $this->maxAge  = $maxAge;
        $this->sMaxAge = $sMaxAge;
        $this->expires = $expires;
    }

    public function setMaxAge($maxAge)
    {
        $this->maxAge = $maxAge;
    }

    public function setSMaxAge($sMaxAge)
    {
        $this->sMaxAge = $sMaxAge;
    }

    public function setExpires(\DateTime $expires)
    {
        $this->expires = $expires;
    }

    public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain)
    {
        $cacheControl = array();

        if ($this->flags & self::TYPE_PUBLIC) {
            $cacheControl[] = 'public';
        }

        if ($this->flags & self::TYPE_PRIVATE) {
            $cacheControl[] = 'private';
        }

        if ($this->flags & self::NO_CACHE) {
            $cacheControl[] = 'no-cache';
        }

        if ($this->flags & self::NO_STORE) {
            $cacheControl[] = 'no-store';
        }

        if ($this->flags & self::NO_TRANSFORM) {
            $cacheControl[] = 'no-transform';
        }

        if ($this->flags & self::MUST_REVALIDATE) {
            $cacheControl[] = 'must-revalidate';
        }

        if ($this->flags & self::PROXY_REVALIDATE) {
            $cacheControl[] = 'proxy-revalidate';
        }

        if ($this->maxAge !== null) {
            $cacheControl[] = 'max-age=' . intval($this->maxAge);
        }

        if ($this->sMaxAge !== null) {
            $cacheControl[] = 's-maxage=' . intval($this->sMaxAge);
        }

        if (!empty($cacheControl)) {
            $response->setHeader('Cache-Control', implode(', ', $cacheControl));
        }

        if ($this->expires !== null) {
            $response->setHeader('Expires', $this->expires->format(DateTime::HTTP));
        }

        $filterChain->handle($request, $response);
    }

    public static function expires(\DateTime $expires)
    {
        return new self(0, null, null, $expires);
    }

    public static function cacheControl($flags = 0, $maxAge = null, $sMaxAge = null)
    {
        return new self($flags, $maxAge, $sMaxAge);
    }

    public static function preventCache()
    {
        return new self(
            self::NO_STORE | self::NO_CACHE | self::MUST_REVALIDATE,
            null,
            null,
            new \DateTime('1986-10-09')
        );
    }
}
