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

namespace PSX\Api\View\Generator;

use PSX\Api\View;
use PSX\Api\View\GeneratorAbstract;
use PSX\Data\Schema\Generator as SchemaGenerator;

/**
 * Raml
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Raml extends GeneratorAbstract
{
	protected $title;
	protected $version;
	protected $baseUri;
	protected $targetNamespace;

	public function __construct($title, $version, $baseUri, $targetNamespace)
	{
		$this->title           = $title;
		$this->version         = $version;
		$this->baseUri         = $baseUri;
		$this->targetNamespace = $targetNamespace;
	}

	public function generate(View $view)
	{
		$raml = '#%RAML 0.8' . "\n";
		$raml.= '---' . "\n";
		$raml.= 'baseUri: ' . $this->baseUri . "\n";
		$raml.= 'version: v' . $this->version . "\n";
		$raml.= 'title: ' . $this->title . "\n";
		$raml.= ($view->getPath() ?: '/') . ':' . "\n";

		$methods   = View::getMethods();
		$generator = new SchemaGenerator\JsonSchema($this->targetNamespace);

		foreach($methods as $method => $methodName)
		{
			if($view->has($method))
			{
				$raml.= '  ' . strtolower($methodName) . ':' . "\n";

				if($view->has($method | View::TYPE_REQUEST))
				{
					$schema = $generator->generate($view->get($method | View::TYPE_REQUEST));
					$schema = str_replace("\n", "\n          ", $schema);

					$raml.= '    body:' . "\n";
					$raml.= '      application/json:' . "\n";
					$raml.= '        schema: |' . "\n";
					$raml.= '          ' . $schema . "\n";
				}

				if($view->has($method | View::TYPE_RESPONSE))
				{
					$schema = $generator->generate($view->get($method | View::TYPE_RESPONSE));
					$schema = str_replace("\n", "\n              ", $schema);

					$raml.= '    responses:' . "\n";
					$raml.= '      200:' . "\n";
					$raml.= '        body:' . "\n";
					$raml.= '          application/json:' . "\n";
					$raml.= '            schema: |' . "\n";
					$raml.= '              ' . $schema . "\n";
				}
			}
		}

		return $raml;
	}
}
