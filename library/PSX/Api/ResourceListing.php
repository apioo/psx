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
use PSX\Api\Documentation;
use PSX\Cache;
use PSX\Data\Schema;
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
	protected $cache;

	public function __construct(RoutingParserInterface $routingParser, ControllerFactoryInterface $controllerFactory, Cache $cache)
	{
		$this->routingParser     = $routingParser;
		$this->controllerFactory = $controllerFactory;
		$this->cache             = $cache;
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
		return $this->getCachedResources($request, $response, $context);
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
		$matcher   = new PathMatcher($sourcePath);
		$resources = $this->getCachedResources($request, $response, $context);

		foreach($resources as $resource)
		{
			if($matcher->match($resource->getPath()))
			{
				return $resource;
			}
		}

		return null;
	}

	protected function getCachedResources(RequestInterface $request, ResponseInterface $response, Context $context)
	{
		$item = $this->cache->getItem('cached-resources');

		if($item->isHit())
		{
			return $item->get();
		}
		else
		{
			$resources = $this->getActualResources($request, $response, $context);
			$result    = array();

			foreach($resources as $resource)
			{
				$result[] = $this->getMaterializedResource($resource);
			}

			$item->set($result);

			$this->cache->save($item);

			return $result;
		}
	}

	protected function getActualResources(RequestInterface $request, ResponseInterface $response, Context $context)
	{
		$collections = $this->getCachedRouting();
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

	protected function getMaterializedResource(Resource $resource)
	{
		$versions      = $resource->getDocumentation()->getViews();
		$documentation = new Documentation\Version($resource->getDocumentation()->getDescription());

		foreach($versions as $version => $views)
		{
			$newView = new View($views->getStatus(), $views->getPath());

			foreach($views as $type => $view)
			{
				$newView->set($type, new Schema($view->getDefinition()));
			}

			$documentation->addView($version, $newView);
		}

		$resource->setDocumentation($documentation);

		return $resource;
	}

	protected function getCachedRouting()
	{
		$item = $this->cache->getItem('cached-routing');

		if($item->isHit())
		{
			return $item->get();
		}
		else
		{
			$collections = $this->routingParser->getCollection();

			$item->set($collections);

			$this->cache->save($item);

			return $collections;
		}
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
