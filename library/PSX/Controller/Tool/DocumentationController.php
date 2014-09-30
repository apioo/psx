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
use PSX\Data\SchemaInterface;
use PSX\Exception;

/**
 * DocumentationController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DocumentationController extends ViewAbstract
{
	const EXPORT_XSD        = 1;
	const EXPORT_JSONSCHEMA = 2;
	const EXPORT_HTML       = 3;

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

		$path    = $this->getParameter('path');
		$export  = $this->getParameter('export');
		$version = $this->getParameter('version');
		$method  = $this->getParameter('method');
		$type    = $this->getParameter('type');

		if(!empty($path))
		{
			$resource = $this->getResource($path);

			if(!empty($export))
			{
				$view   = $resource->doc->getView($version);
				$schema = $view->get($method, $type);
				$body   = null;

				if(!$schema instanceof SchemaInterface)
				{
					throw new Exception('Schema not available');
				}

				if($export == self::EXPORT_XSD)
				{
					$this->response->setHeader('Content-Type', 'application/xml');

					$generator = new Generator\Xsd($this->config->get('psx_url'));
					$body      = $generator->generate($schema);
				}
				else if($export == self::EXPORT_JSONSCHEMA)
				{
					$this->response->setHeader('Content-Type', 'application/json');

					$generator = new Generator\JsonSchema($this->config->get('psx_url'));
					$body      = $generator->generate($schema);
				}
				else if($export == self::EXPORT_HTML)
				{
					$this->response->setHeader('Content-Type', 'text/html');

					$generator = new Generator\Html($this->config->get('psx_url'));
					$body      = $this->getHtmlTemplate($generator->generate($schema));
				}
				else
				{
					throw new Exception('Invalid generator');
				}

				$this->setBody($body);
			}
			else
			{
				$views    = $resource->doc->getViews();
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
					'method'     => $resource->routing[0],
					'path'       => $resource->routing[1],
					'controller' => $resource->routing[2],
					'versions'   => $versions,
				));
			}
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

	protected function getResource($sourcePath)
	{
		$collections = $this->routingParser->getCollection();
		$routings    = array();

		foreach($collections as $collection)
		{
			list($methods, $path, $source) = $collection;

			$parts     = explode('::', $source, 2);
			$className = isset($parts[0]) ? $parts[0] : null;

			if(class_exists($className) && $sourcePath == $path)
			{
				$controller = $this->controllerFactory->getController($className, $this->location, $this->request, $this->response);

				if($controller instanceof DocumentedInterface)
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

	protected function getHtmlTemplate($body)
	{
		return <<<HTML
<!DOCTYPE>
<html>
<head>
	<title></title>
	<style type="text/css">
	body
	{
		font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;
	}

	h1
	{
		padding:10px;
		background-color:#222;
		color:#fff;
		font-size:1.4em;
	}

	.table
	{
		width:100%;
	}

	.table th
	{
		border-bottom:2px solid #ccc;
		text-align:left;
		padding:6px;
	}

	.table td
	{
		padding:6px;
		border-bottom:1px solid #eee;
	}

	.property-required
	{
		font-weight:bold;
	}
	</style>
</head>
<body>

{$body}

</body>
</html>
HTML;
	}
}
