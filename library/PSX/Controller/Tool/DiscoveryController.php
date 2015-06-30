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

use PSX\Controller\ApiAbstract;
use PSX\Data\Object;

/**
 * DiscoveryController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DiscoveryController extends ApiAbstract
{
	/**
	 * @Inject
	 * @var \PSX\Loader\ReverseRouter
	 */
	protected $reverseRouter;

	public function onGet()
	{
		parent::onGet();

		$links = [];

		$apiPath = $this->reverseRouter->getDispatchUrl();
		if($apiPath !== null)
		{
			$links[] = new Object([
				'rel'  => 'api',
				'href' => $apiPath,
			]);
		}

		$routingPath = $this->reverseRouter->getUrl('PSX\Controller\Tool\RoutingController');
		if($routingPath !== null)
		{
			$links[] = new Object([
				'rel'  => 'routing',
				'href' => $routingPath,
			]);
		}

		$commandPath = $this->reverseRouter->getUrl('PSX\Controller\Tool\CommandController');
		if($commandPath !== null)
		{
			$links[] = new Object([
				'rel'  => 'command',
				'href' => $commandPath,
			]);
		}

		$documentationPath = $this->reverseRouter->getUrl('PSX\Controller\Tool\DocumentationController::doIndex');
		if($documentationPath !== null)
		{
			$links[] = new Object([
				'rel'  => 'documentation',
				'href' => $documentationPath,
			]);
		}

		$ramlGeneratorPath = $this->reverseRouter->getUrl('PSX\Controller\Tool\RamlGeneratorController', ['{version}', '{path}']);
		if($ramlGeneratorPath !== null)
		{
			$links[] = new Object([
				'rel'  => 'raml',
				'href' => $ramlGeneratorPath,
			]);
		}

		$wsdlGeneratorPath = $this->reverseRouter->getUrl('PSX\Controller\Tool\WsdlGeneratorController', ['{version}', '{path}']);
		if($wsdlGeneratorPath !== null)
		{
			$links[] = new Object([
				'rel'  => 'wsdl',
				'href' => $wsdlGeneratorPath,
			]);
		}

		$swaggerGeneratorPath = $this->reverseRouter->getUrl('PSX\Controller\Tool\SwaggerGeneratorController::doDetail', ['{version}', '{path}']);
		if($swaggerGeneratorPath !== null)
		{
			$links[] = new Object([
				'rel'  => 'swagger',
				'href' => $swaggerGeneratorPath,
			]);
		}

		$this->setBody([
			'links' => $links,
		]);
	}
}
