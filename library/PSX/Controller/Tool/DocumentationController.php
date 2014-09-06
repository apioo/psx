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

use PSX\Api\DocumentedInterface;
use PSX\Api\View;
use PSX\Controller\ViewAbstract;
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
			$schema   = $this->getSchema($path);
			$views    = $schema->doc->getViews();
			$versions = array();

			krsort($views);

			foreach($views as $version => $view)
			{
				$versions[] = array(
					'version' => $version,
					'status'  => $view->getStatus(),
					'view'    => $this->getDataFromView($view),
				);
			}

			$this->setBody(array(
				'method'     => $schema->routing[0],
				'path'       => $schema->routing[1],
				'controller' => $schema->routing[2],
				'versions'   => $versions,
			));
		}
		else
		{
			$this->setBody(array(
				'routings' => $this->getRoutings()
			));
		}
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

				if($controller instanceof DocumentedInterface)
				{
					$routings[] = array(
						'method'     => $methods, 
						'path'       => $path, 
						'controller' => $className
					);
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

				if($controller instanceof DocumentedInterface && $sourcePath == $path)
				{
					$obj = new \stdClass();
					$obj->routing = array($methods, $path, $className);
					$obj->doc = $controller->getDocumentation();

					return $obj;
				}
			}
		}

		throw new \Exception('Invalid path');
	}

	protected function getDataFromView(View $view)
	{
		$html = new Generator\Html();
		$data = array();

		if($view->hasGetResponse())
		{
			$data['get_response'] = $html->generate($view->getGetResponse());
		}

		if($view->hasPostRequest())
		{
			$data['post_request'] = $html->generate($view->getPostRequest());
		}

		if($view->hasPostResponse())
		{
			$data['post_response'] = $html->generate($view->getPostResponse());
		}

		if($view->hasPutRequest())
		{
			$data['put_request'] = $html->generate($view->getPutRequest());
		}

		if($view->hasPutResponse())
		{
			$data['put_response'] = $html->generate($view->getPutResponse());
		}

		if($view->hasDeleteRequest())
		{
			$data['delete_request'] = $html->generate($view->getDeleteRequest());
		}

		if($view->hasDeleteResponse())
		{
			$data['delete_response'] = $html->generate($view->getDeleteResponse());
		}

		return $data;
	}
}
