<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Dispatch\Filter;

use PSX\ControllerInterface;
use PSX\Dispatch\FilterChainInterface;
use PSX\Dispatch\FilterInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Loader\Context;

/**
 * ControllerExecutor
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ControllerExecutor implements FilterInterface
{
	protected $controller;
	protected $context;

	public function __construct(ControllerInterface $controller, Context $context)
	{
		$this->controller = $controller;
		$this->context    = $context;
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

		$method = $this->context->get(Context::KEY_METHOD);

		if(!empty($method) && is_callable([$this->controller, $method]))
		{
			call_user_func_array([$this->controller, $method], array());
		}

		$this->controller->processResponse();

		$filterChain->handle($request, $response);
	}
}
