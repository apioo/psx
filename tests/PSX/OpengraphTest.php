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

namespace PSX;

use PSX\Http\Authentication;
use PSX\Http\GetRequest;
use PSX\Http\Handler\Callback;
use PSX\Http\Response;
use PSX\Http\ResponseParser;

/**
 * OpengraphTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class OpengraphTest extends \PHPUnit_Framework_TestCase
{
	public function testDiscover()
	{
		$testCase = $this;
		$http = new Http(new Callback(function($request) use ($testCase){

			$response = <<<TEXT
HTTP/1.1 200 OK
Date: Thu, 26 Sep 2013 16:36:26 GMT
Content-Type: application/json; charset=UTF-8

<html xmlns:og="http://ogp.me/ns#">
<head>
	<title>The Rock (1996)</title>
	<meta property="og:title" content="The Rock" />
	<meta property="og:type" content="movie" />
	<meta property="og:url" content="http://www.imdb.com/title/tt0117500/" />
	<meta property="og:image" content="http://ia.media-imdb.com/images/rock.jpg" />
</head>
<body>
	<h1>Open Graph protocol</h1>
	<p>The Open Graph protocol enables any web page to become a rich object in a social graph.</p>
</body>
</html>
TEXT;

			return Response::convert($response, ResponseParser::MODE_LOOSE)->toString();

		}));

		$og   = new Opengraph($http);
		$data = $og->discover(new Url('http://127.0.0.1/opengraph'));

		$this->assertEquals($data->get('og:title'), 'The Rock');
		$this->assertEquals($data->get('og:url'), 'http://www.imdb.com/title/tt0117500/');
	}
}

