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

namespace PSX\Http;

use PSX\Http;
use PSX\Http\Stream\StringStream;
use PSX\Url;

/**
 * RequestTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
	public function testToString()
	{
		$body = new StringStream();
		$body->write('foobar');

		$request = new Request(new Url('http://127.0.0.1'), 'POST');
		$request->setHeader('Content-Type', 'text/html; charset=UTF-8');
		$request->setBody($body);

		$httpRequest = 'POST / HTTP/1.1' . Http::$newLine;
		$httpRequest.= 'host: 127.0.0.1' . Http::$newLine;
		$httpRequest.= 'content-type: text/html; charset=UTF-8' . Http::$newLine;
		$httpRequest.= Http::$newLine;
		$httpRequest.= 'foobar';

		$this->assertEquals($httpRequest, (string) $request);
	}

	public function testFragmentEncoding()
	{
		$request = new Request(new Url('http://127.0.0.1/foobar?bar=foo#!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~'), 'POST');
		$request->setHeader('Content-Type', 'text/html; charset=UTF-8');

		$httpRequest = 'POST /foobar?bar=foo#!%22%23$%25&%27()*+,-./0123456789:;%3C=%3E?@ABCDEFGHIJKLMNOPQRSTUVWXYZ%5B\'%5D%5E_%60abcdefghijklmnopqrstuvwxyz%7B%7C%7D~ HTTP/1.1' . Http::$newLine;
		$httpRequest.= 'host: 127.0.0.1' . Http::$newLine;
		$httpRequest.= 'content-type: text/html; charset=UTF-8' . Http::$newLine;
		$httpRequest.= Http::$newLine;

		$this->assertEquals($httpRequest, (string) $request);
	}
}
