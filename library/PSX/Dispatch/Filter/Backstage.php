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
use PSX\Dispatch\FilterChainInterface;
use PSX\Dispatch\FilterInterface;
use PSX\Loader;

/**
 * Inspired by rubys rack backstage. If the specified file exists it get 
 * written as response i.e. to show an maintenance message else the next filter
 * gets called. Note the message gets only displayed for text/html visitors all
 * other requests get passed to the application
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Backstage implements FilterInterface
{
	protected $file;

	public function __construct($file)
	{
		$this->file = $file;
	}

	public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain)
	{
		$accept = $request->getHeader('Accept');

		if(stripos($accept, 'text/html') !== false && is_file($this->file))
		{
			$response->setHeader('Content-Type', 'text/html');
			$response->getBody()->write(file_get_contents($this->file));
		}
		else
		{
			$filterChain->handle($request, $response);
		}
	}
}
