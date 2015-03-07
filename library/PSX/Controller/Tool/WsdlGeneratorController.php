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
use PSX\Api\ResourceListing\Resource;
use PSX\Api\View;
use PSX\Api\View\Generator;
use PSX\Controller\ViewAbstract;
use PSX\Http\Exception as HttpException;
use PSX\Loader\Context;
use PSX\Loader\PathMatcher;

/**
 * WsdlGeneratorController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class WsdlGeneratorController extends ViewAbstract
{
	/**
	 * @Inject
	 * @var PSX\Api\ResourceListing
	 */
	protected $resourceListing;

	public function onGet()
	{
		parent::onGet();

		$version  = $this->getUriFragment('version');
		$path     = $this->getUriFragment('path');
		$resource = $this->resourceListing->getResource($path, $this->request, $this->response, $this->context);

		if($resource instanceof Resource)
		{
			$view = $resource->getDocumentation()->getView($version);

			if(!$view instanceof View)
			{
				throw new HttpException\NotFoundException('Given version is not available');
			}

			$name            = $resource->getName();
			$endpoint        = $this->config['psx_url'] . '/' . $this->config['psx_dispatch'] . ltrim($resource->getPath(), '/');
			$targetNamespace = $this->config['psx_soap_namespace'];

			$this->response->setHeader('Content-Type', 'text/xml');

			$generator = new Generator\Wsdl($name, $endpoint, $targetNamespace);

			$this->setBody($generator->generate($view));
		}
		else
		{
			throw new HttpException\NotFoundException('Invalid resource');
		}
	}
}
