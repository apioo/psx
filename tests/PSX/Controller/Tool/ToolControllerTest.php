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

use DOMDocument;
use PSX\Http\Stream\TempStream;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Json;
use PSX\Test\ControllerTestCase;
use PSX\Url;

/**
 * ToolControllerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ToolControllerTest extends ControllerTestCase
{
	public function testIndex()
	{
		$body     = new TempStream(fopen('php://memory', 'r+'));
		$request  = new Request(new Url('http://127.0.0.1/tool'), 'GET');
		$response = new Response();
		$response->setBody($body);

		$config     = getContainer()->get('config');
		$basePath   = rtrim(parse_url($config['psx_url'], PHP_URL_PATH), '/') . '/' . $config['psx_dispatch'];
		$controller = $this->loadController($request, $response);
		$json       = (string) $body;

		$expect = array(
			'paths'   => array(
				'general' => array(array(
					'title' => 'Routing',
					'path'  => $basePath . 'routing',
				),array(
					'title' => 'Command',
					'path'  => $basePath . 'command',
				)),
				'api'     => array(array(
					'title' => 'Console',
					'path'  => $basePath . 'rest',
				),array(
					'title' => 'Documentation',
					'path'  => $basePath . 'doc',
				)),
			),
			'current' => array(
				'title' => 'Routing',
				'path'  => $basePath . 'routing',
			),
		);

		$this->assertJsonStringEqualsJsonString(json_encode($expect), $json);
	}

	protected function getPaths()
	{
		return array(
			[['GET'], '/tool', 'PSX\Controller\Tool\ToolController'],
			[['GET'], '/routing', 'PSX\Controller\Tool\RoutingController'],
			[['GET', 'POST'], '/command', 'PSX\Controller\Tool\CommandController'],
			[['GET'], '/rest', 'PSX\Controller\Tool\RestController'],
			[['GET'], '/doc', 'PSX\Controller\Tool\DocumentationController::doIndex'],
			[['GET'], '/doc/:version/*path', 'PSX\Controller\Tool\DocumentationController::doDetail'],
		);
	}
}
