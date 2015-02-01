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

namespace PSX\Dispatch;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use RuntimeException;

/**
 * FilterChain
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class FilterChain implements FilterChainInterface, LoggerAwareInterface
{
	protected $filters;
	protected $filterChain;
	protected $logger;

	public function __construct(array $filters, FilterChain $filterChain = null)
	{
		$this->filters     = $filters;
		$this->filterChain = $filterChain;
	}

	public function setLogger(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function handle(RequestInterface $request, ResponseInterface $response)
	{
		$filter = array_shift($this->filters);

		if($filter === null)
		{
			// if we have no filters check whether we have another filter chain
			// which should be called next
			if($this->filterChain !== null)
			{
				$this->filterChain->handle($request, $response, $this->filterChain);
			}
		}
		else if($filter instanceof FilterInterface)
		{
			if($this->logger !== null)
			{
				$this->logger->info('Filter execute ' . get_class($filter));
			}

			$filter->handle($request, $response, $this);
		}
		else if(is_callable($filter))
		{
			call_user_func_array($filter, array($request, $response, $this));
		}
		else
		{
			throw new RuntimeException('Invalid filter value');
		}
	}
}
