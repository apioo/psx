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

use PSX\Dispatch\FilterChainInterface;
use PSX\Dispatch\FilterInterface;
use PSX\Http\Exception\ForbiddenException;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * Filters an incomming request based on the request IP. Only IPs which are 
 * listed in the $allowedIps array can access the application. Note if the IP is
 * not available in the REMOTE_ADDR field of the environment variables (cli) the
 * request can access the application
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class IpFirewall implements FilterInterface
{
	protected $allowedIps;

	public function __construct(array $allowedIps)
	{
		$this->allowedIps = $allowedIps;
	}

	public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain)
	{
		$ip = $this->getIp();

		if($ip === null || in_array($ip, $this->allowedIps))
		{
			$filterChain->handle($request, $response);
		}
		else
		{
			throw new ForbiddenException('Access not allowed');
		}
	}

	protected function getIp()
	{
		return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
	}
}
