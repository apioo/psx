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
use PSX\Http\Response;
use PSX\Http\ResponseParser;

/**
 * PingbackTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class PingbackTest extends \PHPUnit_Framework_TestCase
{
	public function testSend()
	{
		$testCase = $this;
		$http = new Http(new Callback(function($request) use ($testCase){

			if($request->getUrl()->getPath() == '/server')
			{
				$response = <<<TEXT
HTTP/1.1 200 OK
Date: Thu, 26 Sep 2013 16:36:26 GMT
Content-Type: application/xml; charset=UTF-8

<?xml version="1.0" encoding="UTF-8"?>
<methodResponse>
  <params>
    <param>
      <value>
        <string>Successful</string>
      </value>
    </param>
  </params>
</methodResponse>
TEXT;
			}
			else if($request->getUrl()->getPath() == '/resource')
			{
				$response = <<<TEXT
HTTP/1.1 200 OK
Date: Thu, 26 Sep 2013 16:36:26 GMT
Content-Type: text/html; charset=UTF-8
X-Pingback: http://127.0.0.1/server

<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>PSX Testarea</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="X-XRDS-Location" content="http://test.phpsx.org/yadis/xrds" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta name="generator" content="psx" />
	<link rel="pingback" href="http://test.phpsx.org/pingback/server" />
</head>
<body>

<h1>Pingback-enabled resources</h1>

</body>
</html>
TEXT;
			}

			return Response::convert($response, ResponseParser::MODE_LOOSE)->toString();

		}));

		$pingback = new Pingback($http);
		$response = $pingback->send('http://foobar.com', 'http://127.0.0.1/resource');

		$this->assertEquals(true, $response);
	}
}
