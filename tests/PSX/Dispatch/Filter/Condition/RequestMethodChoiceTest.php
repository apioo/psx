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

namespace PSX\Dispatch\Filter\Condition;

use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Stream\StringStream;
use PSX\Url;

/**
 * RequestMethodChoiceTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RequestMethodChoiceTest extends \PHPUnit_Framework_TestCase
{
	public function testCorrectMethod()
	{
		$request  = new Request(new Url('http://localhost'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$filter = $this->getMockBuilder('PSX\Dispatch\FilterInterface')
			->setMethods(array('handle'))
			->getMock();

		$filter->expects($this->once())
			->method('handle')
			->with($this->equalTo($request), $this->equalTo($response));

		$filterChain = $this->getMockBuilder('PSX\Dispatch\FilterChain')
			->setConstructorArgs(array(array()))
			->setMethods(array('handle'))
			->getMock();

		$filterChain->expects($this->never())
			->method('handle');

		$handle = new RequestMethodChoice(array('GET'), $filter);
		$handle->handle($request, $response, $filterChain);
	}

	public function testWrongMethod()
	{
		$request  = new Request(new Url('http://localhost'), 'GET');
		$response = new Response();
		$response->setBody(new StringStream());

		$filter = $this->getMockBuilder('PSX\Dispatch\FilterInterface')
			->setMethods(array('handle'))
			->getMock();

		$filter->expects($this->never())
			->method('handle');

		$filterChain = $this->getMockBuilder('PSX\Dispatch\FilterChain')
			->setConstructorArgs(array(array()))
			->setMethods(array('handle'))
			->getMock();

		$filterChain->expects($this->once())
			->method('handle')
			->with($this->equalTo($request), $this->equalTo($response));

		$handle = new RequestMethodChoice(array('POST', 'PUT', 'DELETE'), $filter);
		$handle->handle($request, $response, $filterChain);
	}
}
