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
use PSX\Api\DocumentationInterface;
use PSX\Api\Documentation\Generator;
use PSX\Api\Documentation\Data;
use PSX\Api\View;
use PSX\Controller\ViewAbstract;
use PSX\Http\Exception as HttpException;
use PSX\Data\Schema\Generator as SchemaGenerator;
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

	/**
	 * @Inject
	 * @var PSX\Loader\ReverseRouter
	 */
	protected $reverseRouter;

	protected $supportedWriter;

	public function doIndex()
	{
		$this->template->set($this->getTemplateFile());

		$this->setBody(array(
			'metas'    => $this->getMetaLinks(),
			'routings' => $this->getRoutings(),
			'links'    => array(
				array(
					'rel'  => 'self',
					'href' => $this->reverseRouter->getUrl(get_class() . '::doIndex'),
				),
				array(
					'rel'  => 'detail',
					'href' => $this->reverseRouter->getUrl(get_class() . '::doDetail', array('{version}', '{path}')),
				)
			)
		));
	}

	public function doDetail()
	{
		$version = $this->getUriFragment('version');
		$path    = $this->getUriFragment('path');

		if(empty($version) || empty($path))
		{
			throw new HttpException\BadRequestException('Version and path not provided');
		}

		$export = $this->getParameter('export');
		$method = $this->getParameter('method');
		$type   = $this->getParameter('type');

		$resource = $this->getResource($path);

		if(!$resource->doc instanceof DocumentationInterface)
		{
			throw new HttpException\InternalServerErrorException('Controller provides no documentation informations');
		}

		if(!empty($export))
		{
			$view   = $resource->doc->getView($version);
			$schema = $view->get($this->getViewModifier($method, $type));
			$body   = null;

			if(!$schema instanceof SchemaInterface)
			{
				throw new Exception('Schema not available');
			}

			if($export == self::EXPORT_XSD)
			{
				$this->response->setHeader('Content-Type', 'application/xml');

				$generator = new SchemaGenerator\Xsd($this->config->get('psx_url'));
				$body      = $generator->generate($schema);
			}
			else if($export == self::EXPORT_JSONSCHEMA)
			{
				$this->response->setHeader('Content-Type', 'application/json');

				$generator = new SchemaGenerator\JsonSchema($this->config->get('psx_url'));
				$body      = $generator->generate($schema);
			}
			else if($export == self::EXPORT_HTML)
			{
				$this->response->setHeader('Content-Type', 'text/html');

				$generator = new SchemaGenerator\Html($this->config->get('psx_url'));
				$body      = $this->getHtmlTemplate($generator->generate($schema));
			}
			else
			{
				throw new HttpException\BadRequestException('Invalid export type');
			}

			$this->setBody($body);
		}
		else
		{
			$this->supportedWriter = array(WriterInterface::JSON, WriterInterface::XML);

			$view = $resource->doc->getView($version);

			if($view instanceof View)
			{
				$views = $resource->doc->getViews();

				krsort($views);

				$versions = array();
				foreach($views as $key => $row)
				{
					$versions[] = array(
						'version' => $key,
						'status'  => $row->getStatus(),
					);
				}


				$generators = $this->getViewGenerators();
				$data       = array();

				foreach($generators as $name => $generator)
				{
					$result = $generator->generate($resource->routing->path, $view);

					if($result instanceof Data)
					{
						$data[$name] = $result->toArray();
					}
				}

				$this->setBody(array(
					'method'      => $resource->routing->method,
					'path'        => $resource->routing->path,
					'controller'  => $resource->routing->controller,
					'description' => $resource->doc->getDescription(),
					'versions'    => $versions,
					'see_others'  => $this->getSeeOthers($version, $resource->routing->path),
					'view'        => array(
						'version' => $version,
						'status'  => $view->getStatus(),
						'data'    => $data,
					),
				));
			}
			else
			{
				throw new HttpException\BadRequestException('Invalid api version');
			}
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
						'path'    => $path, 
						'version' => $controller->getDocumentation()->getLatestVersion(),
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

			if(class_exists($className) && $sourcePath == ltrim($path, '/'))
			{
				$controller = $this->controllerFactory->getController($className, $this->location, $this->request, $this->response);

				if($controller instanceof DocumentedInterface)
				{
					$routing = new \stdClass();
					$routing->method = $methods;
					$routing->path = $path;
					$routing->controller = $className;

					$obj = new \stdClass();
					$obj->routing = $routing;
					$obj->doc = $controller->getDocumentation();

					return $obj;
				}
			}
		}

		throw new HttpException\NotFoundException('Invalid api path');
	}

	protected function getSeeOthers($version, $path)
	{
		$path   = ltrim($path, '/');
		$result = array();

		$wsdlGeneratorPath = $this->reverseRouter->getAbsolutePath('PSX\Controller\Tool\WsdlGeneratorController', array('version' => $version, 'path' => $path));
		if($wsdlGeneratorPath !== null)
		{
			$result['WSDL'] = $wsdlGeneratorPath;
		}

		$swaggerGeneratorPath = $this->reverseRouter->getAbsolutePath('PSX\Controller\Tool\SwaggerGeneratorController', array('version' => $version, 'path' => $path));
		if($swaggerGeneratorPath !== null)
		{
			$result['Swagger'] = $swaggerGeneratorPath;
		}

		return $result;
	}

	protected function getViewModifier($method, $type)
	{
		return $this->getMethodParameter($method) | $this->getTypeParameter($type);
	}

	protected function getMethodParameter($method)
	{
		$methodMap = array(
			'GET'    => View::METHOD_GET,
			'POST'   => View::METHOD_POST,
			'PUT'    => View::METHOD_PUT,
			'DELETE' => View::METHOD_DELETE,
		);

		if(isset($methodMap[$method]))
		{
			return $methodMap[$method];
		}
		else
		{
			throw new HttpException\BadRequestException('Invalid method parameter');
		}
	}

	protected function getTypeParameter($type)
	{
		$typeMap = array(
			0 => View::TYPE_REQUEST,
			1 => View::TYPE_RESPONSE,
		);

		if(isset($typeMap[$type]))
		{
			return $typeMap[$type];
		}
		else
		{
			throw new HttpException\BadRequestException('Invalid type parameter');
		}
	}

	protected function getHtmlTemplate($body)
	{
		return <<<HTML
<!DOCTYPE>
<html>
<head>
	<title>Html</title>
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

	protected function getTemplateFile()
	{
		return __DIR__ . '/../Resource/documentation_controller.tpl';
	}

	protected function getViewGenerators()
	{
		return array(
			'Schema' => new Generator\Schema(new SchemaGenerator\Html()),
		);
	}

	protected function getMetaLinks()
	{
		return array();
	}

	protected function getSupportedWriter()
	{
		if($this->supportedWriter === null)
		{
			return parent::getSupportedWriter();
		}
		else
		{
			return $this->supportedWriter;
		}
	}
}
