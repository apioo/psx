<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use PSX\DateTime;
use PSX\Dispatch\FilterChainInterface;
use PSX\Dispatch\FilterInterface;

/**
 * Uses http headers to controle the browser cache of the client
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

	public function setExpires(\DateTime $expries)
	{
		$this->expires = $expires;
	}

	public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain)
	{
		$cacheControl = array();

		if($this->flags & self::TYPE_PUBLIC)
		{
			$cacheControl[] = 'public';
		}

		if($this->flags & self::TYPE_PRIVATE)
		{
			$cacheControl[] = 'private';
		}

		if($this->flags & self::NO_CACHE)
		{
			$cacheControl[] = 'no-cache';
		}

		if($this->flags & self::NO_STORE)
		{
			$cacheControl[] = 'no-store';
		}

		if($this->flags & self::NO_TRANSFORM)
		{
			$cacheControl[] = 'no-transform';
		}

		if($this->flags & self::MUST_REVALIDATE)
		{
			$cacheControl[] = 'must-revalidate';
		}

		if($this->flags & self::PROXY_REVALIDATE)
		{
			$cacheControl[] = 'proxy-revalidate';
		}

		if($this->maxAge !== null)
		{
			$cacheControl[] = 'max-age=' . intval($this->maxAge);
		}

		if($this->sMaxAge !== null)
		{
			$cacheControl[] = 's-maxage=' . intval($this->sMaxAge);
		}

		if(!empty($cacheControl))
		{
			$response->setHeader('Cache-Control', implode(', ', $cacheControl));
		}

		if($this->expires !== null)
		{
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
