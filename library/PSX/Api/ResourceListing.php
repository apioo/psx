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

namespace PSX\Api;

use PSX\Api\ResourceListing\Resource;
use PSX\Api\View;
use PSX\Dispatch\ControllerFactoryInterface;
use PSX\Loader\Context;
use PSX\Loader\RoutingParserInterface;
use PSX\Loader\PathMatcher;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * ResourceListing
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResourceListing
{
	protected $routingParser;
	protected $controllerFactory;

	public function __construct(RoutingParserInterface $routingParser, ControllerFactoryInterface $controllerFactory)
	{
		$this->routingParser     = $routingParser;
		$this->controllerFactory = $controllerFactory;
	}

	/**
	 * Returns all documented API resources
	 *
	 * @param PSX\Http\RequestInterface $request
	 * @param PSX\Http\ResponseInterface $response
	 * @param PSX\Loader\Context $context
	 * @return array<PSX\Api\Documentation\ResourceListing\Resource>
	 */
	public function getResources(RequestInterface $request, ResponseInterface $response, Context $context)
	{
		$collections = $this->routingParser->getCollection();
		$result      = array();

		foreach($collections as $collection)
		{
			list($methods, $path, $source) = $collection;

			$parts     = explode('::', $source, 2);
			$className = isset($parts[0]) ? $parts[0] : null;

			if(class_exists($className))
			{
				$ctx = clone $context;
				$ctx->set(Context::KEY_PATH, $path);

				$controller = $this->getController($className, $request, $response, $ctx);

				if($controller instanceof DocumentedInterface)
				{
					$name = substr(strrchr(get_class($controller), '\\'), 1);
					$doc  = $controller->getDocumentation();

					if($doc instanceof DocumentationInterface)
					{
						$result[] = new Resource(
							$name,
							$methods,
							$path,
							$source,
							$doc
						);
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Returns an specific API resource for the given path
	 *
	 * @param string $sourcePath
	 * @param PSX\Http\RequestInterface $request
	 * @param PSX\Http\ResponseInterface $response
	 * @param PSX\Loader\Context $context
	 * @return PSX\Api\Documentation\ResourceListing\Resource
	 */
	public function getResource($sourcePath, RequestInterface $request, ResponseInterface $response, Context $context)
	{
		$matcher     = new PathMatcher($sourcePath);
		$collections = $this->routingParser->getCollection();

		foreach($collections as $collection)
		{
			list($methods, $path, $source) = $collection;

			$parts     = explode('::', $source, 2);
			$className = isset($parts[0]) ? $parts[0] : null;

			if(class_exists($className) && $matcher->match($path))
			{
				$ctx = clone $context;
				$ctx->set(Context::KEY_PATH, $path);

				$controller = $this->getController($className, $request, $response, $ctx);

				if($controller instanceof DocumentedInterface)
				{
					$name = substr(strrchr(get_class($controller), '\\'), 1);
					$doc  = $controller->getDocumentation();

					if($doc instanceof DocumentationInterface)
					{
						return new Resource(
							$name,
							$methods,
							$path,
							$source,
							$doc
						);
					}
				}
			}
		}

		return null;
	}

	protected function getController($className, RequestInterface $request, ResponseInterface $response, Context $context)
	{
		try
		{
			return $this->controllerFactory->getController($className, $request, $response, $context);
		}
		catch(\Exception $e)
		{
			return null;
		}
	}
}
