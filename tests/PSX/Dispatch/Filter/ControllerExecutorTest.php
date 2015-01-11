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

use PSX\Dispatch\FilterChain;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\TempStream;
use PSX\Url;
use PSX\Loader;
use PSX\Loader\Callback;

/**
 * ControllerExecutorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ControllerExecutorTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider requestMethodProvider
	 */
	public function testExecuteRequestMethod($method)
	{
		$request  = new Request(new Url('http://localhost'), $method);
		$response = new Response();

		$controller = $this->getMock('PSX\ControllerInterface', array(
			'onLoad', 
			'onDelete', 
			'onGet', 
			'onHead', 
			'onOptions', 
			'onPost', 
			'onPut', 
			'onTrace', 
			'processResponse',
		));

		$controller->expects($this->once())
			->method('onLoad');

		$controller->expects($this->once())
			->method('on' . ucfirst(strtolower($method)));

		$controller->expects($this->once())
			->method('processResponse');

		$filters = array();
		$filters[] = new ControllerExecutor($controller);

		$filterChain = new FilterChain($filters);
		$filterChain->handle($request, $response);
	}

	public function requestMethodProvider()
	{
		return array(
			['DELETE'],
			['GET'],
			['HEAD'],
			['OPTIONS'],
			['POST'],
			['PUT'],
			['TRACE'],
		);
	}

	public function testExecuteControllerMethod()
	{
		$controller = $this->getMock('PSX\ControllerInterface', array(
			'onLoad', 
			'onDelete', 
			'onGet', 
			'onHead', 
			'onOptions', 
			'onPost', 
			'onPut', 
			'onTrace', 
			'processResponse',
			'doFoo',
		));

		$controller->expects($this->once())
			->method('onLoad');

		$controller->expects($this->once())
			->method('onGet');

		$controller->expects($this->once())
			->method('doFoo');

		$controller->expects($this->once())
			->method('processResponse');

		$callback = $this->getMockBuilder('PSX\Loader\Callback')
			->disableOriginalConstructor()
			->setMethods(array('getClass', 'getMethod'))
			->getMock();

		$callback->expects($this->once())
			->method('getClass')
			->will($this->returnValue($controller));

		$callback->expects($this->once())
			->method('getMethod')
			->will($this->returnValue('doFoo'));

		$request  = new Request(new Url('http://localhost'), 'GET');
		$request->setAttribute(Loader::REQUEST_CALLBACK, $callback);
		$response = new Response();

		$filters = array();
		$filters[] = new ControllerExecutor($controller);

		$filterChain = new FilterChain($filters);
		$filterChain->handle($request, $response);
	}
}
