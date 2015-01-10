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

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use PSX\ControllerInterface;
use PSX\Dispatch\FilterChainInterface;
use PSX\Dispatch\FilterInterface;
use PSX\Loader;
use PSX\Loader\Callback;

/**
 * ControllerExecutor
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ControllerExecutor implements FilterInterface
{
	protected $controller;

	public function __construct(ControllerInterface $controller)
	{
		$this->controller = $controller;
	}

	public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain)
	{
		$this->controller->onLoad();

		switch($request->getMethod())
		{
			case 'DELETE':
				$this->controller->onDelete();
				break;

			case 'GET':
				$this->controller->onGet();
				break;

			case 'HEAD':
				$this->controller->onHead();
				break;

			case 'OPTIONS':
				$this->controller->onOptions();
				break;

			case 'POST':
				$this->controller->onPost();
				break;

			case 'PUT':
				$this->controller->onPut();
				break;

			case 'TRACE':
				$this->controller->onTrace();
				break;
		}

		$callback = $request->getAttribute(Loader::REQUEST_CALLBACK);

		if($callback instanceof Callback)
		{
			$method = $callback->getMethod();

			if(!empty($method))
			{
				call_user_func_array(array($this->controller, $method), array($request, $response));
			}

			$this->controller->processResponse();
		}

		$filterChain->handle($request, $response);
	}
}
