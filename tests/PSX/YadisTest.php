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

namespace PSX;

use PSX\Http\Authentication;
use PSX\Http\GetRequest;
use PSX\Http\Handler\Callback;
use PSX\Http\Handler\Mock;
use PSX\Http\Handler\MockCapture;
use PSX\Http\Response;
use PSX\Http\ResponseParser;

/**
 * YadisTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class YadisTest extends \PHPUnit_Framework_TestCase
{
	public function testDiscovery()
	{
		$testCase = $this;
		$http = new Http(new Callback(function($request) use ($testCase){

			if($request->getUrl()->getPath() == '/xrds')
			{
				$response = <<<'TEXT'
HTTP/1.1 200 OK
Date: Thu, 26 Sep 2013 16:36:26 GMT
Content-Type: application/xrds+xml; charset=UTF-8

<?xml version="1.0" encoding="UTF-8"?>
<xrds:XRDS xmlns="xri://$xrd*($v*2.0)" xmlns:xrds="xri://$xrds">
	<XRD>
		<Service>
			<URI>http://test.phpsx.org</URI>
			<Type>http://ns.test.phpsx.org/2011/test</Type>
		</Service>
	</XRD>
</xrds:XRDS>
TEXT;
			}
			else
			{
				$response = <<<TEXT
HTTP/1.1 200 OK
Date: Thu, 26 Sep 2013 16:36:26 GMT
Content-Type: text/html; charset=UTF-8
X-XRDS-Location: http://127.0.0.1/xrds

<html>
<body>
	<h1>Oo</h1>
</body>
</html>
TEXT;
			}

			return Response::convert($response, ResponseParser::MODE_LOOSE)->toString();

		}));

		$yadis = new Yadis($http);
		$xrd   = $yadis->discover(new Url('http://127.0.0.1'));

		$this->assertInstanceOf('PSX\Xri\Xrd', $xrd);

		$service = $xrd->getService();

		$this->assertEquals(1, count($service));
		$this->assertInstanceOf('PSX\Xri\Xrd\Service', $service[0]);
		$this->assertEquals('http://test.phpsx.org', $service[0]->getUri());
		$this->assertEquals(array('http://ns.test.phpsx.org/2011/test'), $service[0]->getType());
	}
}
