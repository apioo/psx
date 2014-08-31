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

use PSX\Controller\ViewAbstract;
use PSX\Controller\SchemaDocumentedInterface;
use PSX\Data\Schema\Generator;
use PSX\Data\WriterInterface;
use PSX\Data\Schema\Documentation;

/**
 * DocumentationController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DocumentationController extends ViewAbstract
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

		$this->template->set(__DIR__ . '/../Resource/documentation_controller.tpl');

		$path = $this->getParameter('path');

		if(!empty($path))
		{
			$html   = new Generator\Html();
			$schema = $this->getSchema($path);

			$data = array(
				'method'     => $schema->routing[0],
				'path'       => $schema->routing[1],
				'controller' => $schema->routing[2],
			);

			if($schema->doc->hasResponse(Documentation::METHOD_GET))
			{
				$data['get_response'] = $html->generate($schema->doc->getResponse(Documentation::METHOD_GET));
			}

			if($schema->doc->hasRequest(Documentation::METHOD_POST))
			{
				$data['post_request'] = $html->generate($schema->doc->getRequest(Documentation::METHOD_POST));
			}

			if($schema->doc->hasResponse(Documentation::METHOD_POST))
			{
				$data['post_response'] = $html->generate($schema->doc->getResponse(Documentation::METHOD_POST));
			}

			if($schema->doc->hasRequest(Documentation::METHOD_PUT))
			{
				$data['put_request'] = $html->generate($schema->doc->getRequest(Documentation::METHOD_PUT));
			}

			if($schema->doc->hasResponse(Documentation::METHOD_PUT))
			{
				$data['put_response'] = $html->generate($schema->doc->getResponse(Documentation::METHOD_PUT));
			}

			if($schema->doc->hasRequest(Documentation::METHOD_DELETE))
			{
				$data['delete_request'] = $html->generate($schema->doc->getRequest(Documentation::METHOD_DELETE));
			}

			if($schema->doc->hasResponse(Documentation::METHOD_DELETE))
			{
				$data['delete_response'] = $html->generate($schema->doc->getResponse(Documentation::METHOD_DELETE));
			}

			$this->setBody($data);
		}
		else
		{
			$this->setBody(array(
				'routings' => $this->getRoutings()
			));
		}
	}

	protected function getSupportedWriter()
	{
		return array(
			WriterInterface::HTML,
			WriterInterface::JSON,
		);
	}

	protected function getRoutings()
	{
		$collections = $this->routingParser->getCollection();
		$routings    = array();

		foreach($collections as $collection)
		{
			list($methods, $path, $source) = $collection;

			$parts     = explode('::', $source, 2);
			$className = isset($parts[0]) ? $parts[0] : null;

			if(class_exists($className))
			{
				$controller = $this->controllerFactory->getController($className, $this->location, $this->request, $this->response);

				if($controller instanceof SchemaDocumentedInterface)
				{
					$routings[] = array($methods, $path, $className);
				}
			}
		}

		return $routings;
	}

	protected function getSchema($sourcePath)
	{
		$collections = $this->routingParser->getCollection();
		$routings    = array();

		foreach($collections as $collection)
		{
			list($methods, $path, $source) = $collection;

			$parts     = explode('::', $source, 2);
			$className = isset($parts[0]) ? $parts[0] : null;

			if(class_exists($className))
			{
				$controller = $this->controllerFactory->getController($className, $this->location, $this->request, $this->response);

				if($controller instanceof SchemaDocumentedInterface && $sourcePath == $path)
				{
					$obj = new \stdClass();
					$obj->routing = array($methods, $path, $className);
					$obj->doc = $controller->getSchemaDocumentation();

					return $obj;
				}
			}
		}

		throw new \Exception('Invalid path');
	}
}
