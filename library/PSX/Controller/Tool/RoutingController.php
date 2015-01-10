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
use PSX\Api\View;
use PSX\Controller\ViewAbstract;
use PSX\Data\Schema\Generator;
use PSX\Data\WriterInterface;
use PSX\Data\Schema\Documentation;

/**
 * RoutingController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RoutingController extends ViewAbstract
{
	/**
	 * @Inject
	 * @var PSX\Loader\RoutingParserInterface
	 */
	protected $routingParser;

	public function onGet()
	{
		parent::onGet();

		$this->template->set(__DIR__ . '/../Resource/routing_controller.tpl');

		$this->setBody(array(
			'routings' => $this->getRoutings(),
		));
	}

	protected function getRoutings()
	{
		$result   = array();
		$routings = $this->routingParser->getCollection()->getAll();

		foreach($routings as $routing)
		{
			list($methods, $path, $source) = $routing;

			$result[] = array(
				'methods' => $methods,
				'path'    => $path,
				'source'  => $source,
			);
		}

		return $result;
	}
}
