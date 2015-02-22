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
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
