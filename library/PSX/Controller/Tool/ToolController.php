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
use PSX\Api\View;
use PSX\Controller\ViewAbstract;
use PSX\Data\Schema\Generator;
use PSX\Data\WriterInterface;
use PSX\Data\Schema\Documentation;

/**
 * ToolController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

		$paths = array();

		$routingPath = $this->reverseRouter->getAbsolutePath('PSX\Controller\Tool\RoutingController');
		if($routingPath !== null)
		{
			$paths[] = array(
				'title' => 'Routing',
				'path'  => $routingPath,
			);
		}

		$documentationPath = $this->reverseRouter->getAbsolutePath('PSX\Controller\Tool\DocumentationController');
		if($documentationPath !== null)
		{
			$paths[] = array(
				'title' => 'Documentation',
				'path'  => $documentationPath,
			);
		}

		$commandPath = $this->reverseRouter->getAbsolutePath('PSX\Controller\Tool\CommandController');
		if($commandPath !== null)
		{
			$paths[] = array(
				'title' => 'Command',
				'path'  => $commandPath,
			);
		}

		$restPath = $this->reverseRouter->getAbsolutePath('PSX\Controller\Tool\RestController');
		if($restPath !== null)
		{
			$paths[] = array(
				'title' => 'Rest',
				'path'  => $restPath,
			);
		}

		$this->setBody(array(
			'paths' => $paths,
			'current' => current($paths),
		));
	}
}
