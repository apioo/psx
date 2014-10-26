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
use PSX\Http\Exception as HttpException;
use PSX\Loader\PathMatcher;
use PSX\Wsdl\Generator;

/**
 * WsdlGeneratorController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class WsdlGeneratorController extends ViewAbstract
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

	public function onGet()
	{
		parent::onGet();

		$version  = $this->getUriFragment('version');
		$path     = $this->getUriFragment('path');
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

		$name            = $resource->name;
		$endpoint        = $this->config['psx_url'] . '/' . $this->config['psx_dispatch'] . ltrim($path, '/');
		$targetNamespace = $this->config['psx_url'];

		$generator = new Generator(Generator::VERSION_1, $name, $endpoint, $targetNamespace);

		$wsdl = $generator->generate($view);
		$wsdl->formatOutput = true;

		$this->setBody($wsdl);
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
}
