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

namespace PSX\Dispatch\Filter;

use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\StringStream;
use PSX\Url;

/**
 * BackstageTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class BackstageTest extends \PHPUnit_Framework_TestCase
{
	public function testFileExists()
	{
		$request  = new Request(new Url('http://localhost'), 'GET', array(
			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'
		));
		$response = new Response();
		$response->setBody(new StringStream());

		$filterChain = $this->getMockBuilder('PSX\Dispatch\FilterChain')
			->setConstructorArgs(array(array()))
			->setMethods(array('handle'))
			->getMock();

		$filterChain->expects($this->never())
			->method('handle');

		$handle = new Backstage(__DIR__ . '/backstage.htm');
		$handle->handle($request, $response, $filterChain);

		$this->assertEquals('foobar', (string) $response->getBody());
	}

	public function testNoFittingAcceptHeader()
	{
		$request  = new Request(new Url('http://localhost'), 'GET', array(
			'Accept' => 'application/json'
		));
		$response = new Response();

		$filterChain = $this->getMockBuilder('PSX\Dispatch\FilterChain')
			->setConstructorArgs(array(array()))
			->setMethods(array('handle'))
			->getMock();

		$filterChain->expects($this->once())
			->method('handle')
			->with($this->equalTo($request), $this->equalTo($response));

		$handle = new Backstage(__DIR__ . '/backstage.htm');
		$handle->handle($request, $response, $filterChain);
	}

	public function testFileNotExists()
	{
		$request  = new Request(new Url('http://localhost'), 'GET', array(
			'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'
		));
		$response = new Response();

		$filterChain = $this->getMockBuilder('PSX\Dispatch\FilterChain')
			->setConstructorArgs(array(array()))
			->setMethods(array('handle'))
			->getMock();

		$filterChain->expects($this->once())
			->method('handle')
			->with($this->equalTo($request), $this->equalTo($response));

		$handle = new Backstage(__DIR__ . '/foo.htm');
		$handle->handle($request, $response, $filterChain);
	}

	public function testNoFittingAcceptHeaderAndFileNotExists()
	{
		$request  = new Request(new Url('http://localhost'), 'GET', array(
			'Accept' => 'application/json'
		));
		$response = new Response();

		$filterChain = $this->getMockBuilder('PSX\Dispatch\FilterChain')
			->setConstructorArgs(array(array()))
			->setMethods(array('handle'))
			->getMock();

		$filterChain->expects($this->once())
			->method('handle')
			->with($this->equalTo($request), $this->equalTo($response));

		$handle = new Backstage(__DIR__ . '/foo.htm');
		$handle->handle($request, $response, $filterChain);
	}
}
