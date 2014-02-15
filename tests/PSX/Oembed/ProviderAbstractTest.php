<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Oembed;

use PSX\Controller\ControllerTestCase;
use PSX\Http;
use PSX\Http\Handler\Callback;
use PSX\Http\GetRequest;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Oauth;
use PSX\OauthTest;
use PSX\Url;

/**
 * ProviderAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ProviderAbstractTest extends ControllerTestCase
{
	public function testRequest()
	{
		$testCase = $this;
		$http = new Http(new Callback(function($request) use ($testCase){

			$body     = new TempStream(fopen('php://memory', 'r+'));
			$response = new Response();
			$response->setBody($body);

			$testCase->loadController($request, $response);

			return $response;

		}));

		$url      = new Url('http://127.0.0.1/oembed?url=http%3A%2F%2F127.0.0.1%2Fresource');
		$request  = new GetRequest($url);
		$response = $http->request($request);

		$expect = array(
			'url'         => 'http://127.0.0.1/resource.png',
			'width'       => 640,
			'height'      => 480,
			'author_name' => 'foobar',
		);

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertJsonStringEqualsJsonString(json_encode($expect), (string) $response->getBody());
	}

	protected function getPaths()
	{
		return array(
			'/oembed' => 'PSX\Oembed\TestProviderAbstract',
		);
	}
}
