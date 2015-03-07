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

namespace PSX\Controller\Tool;

use PSX\Api\DocumentedInterface;
use PSX\Api\DocumentationInterface;
use PSX\Api\View\Generator;
use PSX\Api\View\Generator\HtmlAbstract;
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
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DocumentationController extends ViewAbstract
{
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

		$resource = $this->resourceListing->getResource($path, $this->request, $this->response, $this->context);

		if(!$resource instanceof Resource)
		{
			throw new HttpException\InternalServerErrorException('Controller provides no documentation informations');
		}

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
			$methods    = View::getMethods();
			$data       = array();

			foreach($generators as $name => $generator)
			{
				if($generator instanceof HtmlAbstract)
				{
					$result = array();

					foreach($methods as $method => $methodName)
					{
						$generator->setModifier($method);

						$result[$methodName] = $generator->generate($view);
					}

					$data[$generator->getName()] = $result;
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

	protected function getRoutings()
	{
		$routings  = array();
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

		$swaggerGeneratorPath = $this->reverseRouter->getAbsolutePath('PSX\Controller\Tool\SwaggerGeneratorController::doDetail', array('version' => $version, 'path' => $path));
		if($swaggerGeneratorPath !== null)
		{
			$result['Swagger'] = $swaggerGeneratorPath;
		}

		$ramlGeneratorPath = $this->reverseRouter->getAbsolutePath('PSX\Controller\Tool\RamlGeneratorController', array('version' => $version, 'path' => $path));
		if($ramlGeneratorPath !== null)
		{
			$result['RAML'] = $ramlGeneratorPath;
		}

		return $result;
	}

	protected function getTemplateFile()
	{
		return __DIR__ . '/../Resource/documentation_controller.tpl';
	}

	protected function getViewGenerators()
	{
		return array(
			'Schema' => new Generator\Html\Schema(new SchemaGenerator\Html()),
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
