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

namespace PSX\Controller\Tool;

use PSX\Api\DocumentedInterface;
use PSX\Api\DocumentationInterface;
use PSX\Api\Documentation\Generator;
use PSX\Api\Documentation\Data;
use PSX\Api\ResourceListing\Resource;
use PSX\Api\View;
use PSX\Controller\ViewAbstract;
use PSX\Data\Schema\Generator as SchemaGenerator;
use PSX\Data\WriterInterface;
use PSX\Data\Schema\Documentation;
use PSX\Data\SchemaInterface;
use PSX\Exception;
use PSX\Http\Exception as HttpException;
use PSX\Loader\RoutingCollection;

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
	 * @var PSX\Api\ResourceListing
	 */
	protected $resourceListing;

	/**
	 * @Inject
	 * @var PSX\Loader\ReverseRouter
	 */
	protected $reverseRouter;

	/**
	 * @var array
	 */
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
					'href' => $this->reverseRouter->getUrl(get_class($this) . '::doIndex'),
				),
				array(
					'rel'  => 'detail',
					'href' => $this->reverseRouter->getUrl(get_class($this) . '::doDetail', array('{version}', '{path}')),
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

		$resource = $this->resourceListing->getResource($path, $this->request, $this->response, $this->context);

		if(!$resource instanceof Resource)
		{
			throw new HttpException\InternalServerErrorException('Controller provides no documentation informations');
		}

		if(!empty($export))
		{
			$view   = $resource->getDocumentation()->getView($version);
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

			$view = $resource->getDocumentation()->getView($version);

			if($view instanceof View)
			{
				$views = $resource->getDocumentation()->getViews();

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
					$result = $generator->generate($resource->getPath(), $view);

					if($result instanceof Data)
					{
						$data[$name] = $result->toArray();
					}
				}

				$this->setBody(array(
					'method'      => $resource->getMethods(),
					'path'        => $resource->getPath(),
					'controller'  => $resource->getSource(),
					'description' => $resource->getDocumentation()->getDescription(),
					'versions'    => $versions,
					'see_others'  => $this->getSeeOthers($version, $resource->getPath()),
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
		$resources = $this->resourceListing->getResources($this->request, $this->response, $this->context);

		foreach($resources as $resource)
		{
			$routings[] = array(
				'path'    => $resource->getPath(), 
				'version' => $resource->getDocumentation()->getLatestVersion(),
			);
		}

		return $routings;
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
		$methodMap = array_flip(View::getMethods());

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
