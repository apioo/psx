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

use PSX\Api\DocumentationInterface;
use PSX\Api\DocumentedInterface;
use PSX\Api\Resource;
use PSX\Api\Resource\Generator;
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
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
		$resources       = $this->resourceListing->getResourceIndex();

		foreach($resources as $resource)
		{
			$path = '/*';
			$path.= Generator\Swagger::transformRoute($resource->getPath());

			$resourceListing->addResource(new ResourceObject($path));
		}

		$this->setBody($resourceListing, WriterInterface::JSON);
	}

	public function doDetail()
	{
		$version = $this->getUriFragment('version');
		$path    = $this->getUriFragment('path');
		$doc     = $this->resourceListing->getDocumentation($path);

		if($doc instanceof DocumentationInterface)
		{
			if($version == '*')
			{
				$version = $doc->getLatestVersion();
			}

			$resource = $doc->getResource($version);

			if(!$resource instanceof Resource)
			{
				throw new HttpException\NotFoundException('Given version is not available');
			}

			$baseUri         = $this->config['psx_url'] . '/' . $this->config['psx_dispatch'];
			$targetNamespace = $this->config['psx_json_namespace'];

			$this->response->setHeader('Content-Type', 'application/json');

			$generator = new Generator\Swagger($version, $baseUri, $targetNamespace);

			$this->setBody($generator->generate($resource));
		}
		else
		{
			throw new HttpException\NotFoundException('Invalid resource');
		}
	}
}
