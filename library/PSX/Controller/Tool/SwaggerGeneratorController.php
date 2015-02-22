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

use PSX\Api\DocumentationInterface;
use PSX\Api\DocumentedInterface;
use PSX\Api\ResourceListing\Resource;
use PSX\Api\View;
use PSX\Api\View\Generator;
use PSX\Controller\ViewAbstract;
use PSX\Data\WriterInterface;
use PSX\Http\Exception as HttpException;
use PSX\Loader\Context;
use PSX\Loader\PathMatcher;
use PSX\Swagger\ResourceListing;
use PSX\Swagger\ResourceObject;

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
	 * @var PSX\Api\ResourceListing
	 */
	protected $resourceListing;

	public function doIndex()
	{
		$resourceListing = new ResourceListing('1.0');
		$resources       = $this->resourceListing->getResources($this->request, $this->response, $this->context);

		foreach($resources as $resource)
		{
			$path = '/' . $resource->getDocumentation()->getLatestVersion();
			$path.= Generator\Swagger::transformRoute($resource->getPath());

			$resourceListing->addResource(new ResourceObject($path));
		}

		$this->setBody($resourceListing, WriterInterface::JSON);
	}

	public function doDetail()
	{
		$version = $this->getUriFragment('version');
		$path    = $this->getUriFragment('path');

		$resource   = $this->resourceListing->getResource($path, $this->request, $this->response, $this->context);
		$apiVersion = 1;

		if($resource instanceof Resource)
		{
			$view       = $resource->getDocumentation()->getView($version);
			$apiVersion = $resource->getDocumentation()->getLatestVersion();

			if(!$view instanceof View)
			{
				throw new HttpException\NotFoundException('Given version is not available');
			}

			$baseUri         = $this->config['psx_url'] . '/' . $this->config['psx_dispatch'];
			$targetNamespace = $this->config['psx_json_namespace'];

			$this->response->setHeader('Content-Type', 'application/json');

			$generator = new Generator\Swagger($apiVersion, $baseUri, $targetNamespace);

			$this->setBody($generator->generate($view));
		}
		else
		{
			throw new HttpException\NotFoundException('Invalid resource');
		}
	}
}
