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

namespace PSX\Dispatch\Filter;

use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Url;

/**
 * ContentMd5Test
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ContentMd5Test extends \PHPUnit_Framework_TestCase
{
	public function testHandle()
	{
		$body = new TempStream(fopen('php://memory', 'r+'));
		$body->write('foobar');

		$request  = new Request(new Url('http://localhost'), 'GET');
		$response = new Response();
		$response->setBody($body);

		$filter = new ContentMd5();
		$filter->handle($request, $response);

		$this->assertEquals(md5('foobar'), $response->getHeader('Content-MD5'));
		$this->assertEquals('foobar', (string) $response->getBody());
	}
}
