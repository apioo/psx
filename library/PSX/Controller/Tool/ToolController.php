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
use PSX\Api\View;
use PSX\Controller\ViewAbstract;
use PSX\Data\Object;
use PSX\Data\Schema\Generator;
use PSX\Data\Schema\Documentation;
use PSX\Data\WriterInterface;

/**
 * ToolController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ToolController extends ViewAbstract
{
	/**
	 * @Inject
	 * @var PSX\Loader\ReverseRouter
	 */
	protected $reverseRouter;

	public function onGet()
	{
		parent::onGet();

		$this->template->set(__DIR__ . '/../Resource/tool_controller.tpl');

		$current = null;
		$paths   = new \stdClass();
		$paths->general = array();
		$paths->api     = array();

		$routingPath = $this->reverseRouter->getAbsolutePath('PSX\Controller\Tool\RoutingController');
		if($routingPath !== null)
		{
			$routing = new Object([
				'title' => 'Routing',
				'path ' => $routingPath,
			]);

			$paths->general[] = $routing;

			$current = $routing;
		}

		$commandPath = $this->reverseRouter->getAbsolutePath('PSX\Controller\Tool\CommandController');
		if($commandPath !== null)
		{
			$command = new Object([
				'title' => 'Command',
				'path ' => $commandPath,
			]);

			$paths->general[] = $command;

			if($current === null)
			{
				$current = $command;
			}
		}

		$restPath = $this->reverseRouter->getAbsolutePath('PSX\Controller\Tool\RestController');
		if($restPath !== null)
		{
			$console = new Object([
				'title' => 'Console',
				'path ' => $restPath,
			]);

			$paths->api[] = $console;

			if($current === null)
			{
				$current = $console;
			}
		}

		$documentationPath = $this->reverseRouter->getAbsolutePath('PSX\Controller\Tool\DocumentationController::doIndex');
		if($documentationPath !== null)
		{
			$documentation = new Object([
				'title' => 'Documentation',
				'path ' => $documentationPath,
			]);

			$paths->api[] = $documentation;

			if($current === null)
			{
				$current = $documentation;
			}
		}

		$body = new \stdClass();
		$body->paths = $paths;
		$body->current = $current;

		$this->setBody($body);
	}
}
