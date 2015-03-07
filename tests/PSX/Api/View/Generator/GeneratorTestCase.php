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

use PSX\Loader\Context;
use PSX\Test\ControllerTestCase;

/**
 * GeneratorTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class GeneratorTestCase extends ControllerTestCase
{
	protected function getView()
	{
		$request  = $this->getMock('PSX\Http\RequestInterface');
		$response = $this->getMock('PSX\Http\ResponseInterface');

		$context = new Context();
		$context->set(Context::KEY_PATH, '/foo/bar');

		$documentation = getContainer()->get('controller_factory')
			->getController('PSX\Controller\Foo\Application\SchemaApi\VersionViewController', $request, $response, $context)
			->getDocumentation();

		return $documentation->getView($documentation->getLatestVersion());
	}

	protected function getPaths()
	{
		return array();
	}
}
