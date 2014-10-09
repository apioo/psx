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

namespace PSX\Controller\Tool;

use DOMDocument;
use DOMElement;
use PSX\Api\DocumentationInterface;
use PSX\Api\DocumentedInterface;
use PSX\Api\View;
use PSX\Controller\ViewAbstract;
use PSX\Data\WriterInterface;
use PSX\Http\Exception as HttpException;
use PSX\Loader\PathMatcher;
use PSX\Data\Schema\Generator;
use PSX\Swagger\Api;
use PSX\Swagger\Declaration;
use PSX\Swagger\ResourceListing;
use PSX\Swagger\ResourceObject;
use PSX\Swagger\Operation;
use PSX\Swagger\Parameter;
use PSX\Swagger\Model;

/**
 * SwaggerGeneratorController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SwaggerGeneratorController extends ViewAbstract
{
	/**
	 * @Inject
	 * @var PSX\Loader\RoutingParserInterface
	 */
	protected $routingParser;

	/**
	 * @Inject
	 * @var PSX\Dispatch\ControllerFactory
	 */
	protected $controllerFactory;

	protected $apiVersion = '1.0.0';

	public function onGet()
	{
		parent::onGet();

		$version = $this->getUriFragment('version');
		$path    = $this->getUriFragment('path');

		if(empty($version) && empty($path))
		{
			$this->setBody($this->getResourceListing(), WriterInterface::JSON);
		}
		else
		{
			$resource = $this->getEndpoint($path);

			if($resource->doc instanceof DocumentationInterface)
			{
				$view = $resource->doc->getView($version);

				if(!$view instanceof View)
				{
					throw new HttpException\NotFoundException('Given version is not available');
				}
			}
			else
			{
				throw new HttpException\InternalServerErrorException('Controller provides no documentation informations');
			}

			$declaration = new Declaration($this->apiVersion);
			$declaration->setBasePath($this->config['psx_url'] . '/' . $this->config['psx_dispatch']);
			$declaration->setApis($this->getApis($resource->routing[1], $view));
			$declaration->setModels($this->getModels($view));
			$declaration->setResourcePath($path);

			$this->setBody($declaration, WriterInterface::JSON);
		}
	}

	protected function getResourceListing()
	{
		$resourceListing = new ResourceListing($this->apiVersion);
		$collections     = $this->routingParser->getCollection();

		foreach($collections as $collection)
		{
			list($methods, $path, $source) = $collection;

			$parts     = explode('::', $source, 2);
			$className = isset($parts[0]) ? $parts[0] : null;

			if(class_exists($className))
			{
				$controller = $this->controllerFactory->getController($className, $this->location, $this->request, $this->response);

				if($controller instanceof DocumentedInterface)
				{
					$path = '/' . $controller->getDocumentation()->getLatestVersion() . $path;

					$resourceListing->addResource(new ResourceObject($path));
				}
			}
		}

		return $resourceListing;
	}

	protected function getEndpoint($sourcePath)
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
				$controller = $this->controllerFactory->getController($className, $this->location, $this->request, $this->response);

				if($controller instanceof DocumentedInterface)
				{
					$obj = new \stdClass();
					$obj->name = substr(strrchr(get_class($controller), '\\'), 1);
					$obj->routing = array($methods, $path, $className);
					$obj->doc = $controller->getDocumentation();

					return $obj;
				}
			}
		}

		throw new HttpException\NotFoundException('Invalid path');
	}

	protected function getApis($path, View $views)
	{
		$methods = array(View::METHOD_GET, View::METHOD_POST, View::METHOD_PUT, View::METHOD_DELETE);
		$api     = new Api($path);

		foreach($methods as $method)
		{
			if($views->has($method))
			{
				if($views->has($method | View::TYPE_REQUEST))
				{
					$entityName = $views->get($method | View::TYPE_REQUEST)->getDefinition()->getName();
				}
				else if($views->has($method | View::TYPE_RESPONSE))
				{
					$entityName = $views->get($method | View::TYPE_RESPONSE)->getDefinition()->getName();
				}

				$methodName = $this->getMethodNameByModifier($method);
				$name       = $methodName . ucfirst($entityName);
				$operation  = new Operation(strtoupper($methodName), $name);

				if($views->has($method | View::TYPE_RESPONSE))
				{
					$operation->setType($views->get($method | View::TYPE_RESPONSE)->getDefinition()->getName());
				}
				else
				{
					$operation->setType('void');
				}

				if($views->has($method | View::TYPE_REQUEST))
				{
					$parameter = new Parameter('body', 'body', null, true);
					$parameter->setType($views->get($method | View::TYPE_REQUEST)->getDefinition()->getName());

					$operation->addParameter($parameter);
				}

				$api->addOperation($operation);
			}
		}

		return array($api);
	}

	protected function getModels(View $views)
	{
		$generator = new Generator\JsonSchema($this->config['psx_url']);
		$models    = array();

		foreach($views as $view)
		{
			$data = json_decode($generator->generate($view), true);
			$name = $view->getDefinition()->getName();

			if(!isset($models[$name]))
			{
				$model = new Model($name);
				$model->setProperties($data['properties']);

				$models[$name] = $model;
			}
		}

		return $models;
	}

	protected function getMethodNameByModifier($modifier)
	{
		if($modifier & View::METHOD_GET)
		{
			return 'get';
		}
		else if($modifier & View::METHOD_POST)
		{
			return 'post';
		}
		else if($modifier & View::METHOD_PUT)
		{
			return 'put';
		}
		else if($modifier & View::METHOD_DELETE)
		{
			return 'delete';
		}
	}
}
