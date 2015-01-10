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
use PSX\Http\Stream\TempStream;
use PSX\Url;

/**
 * GzipEncodeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class GzipEncodeTest extends \PHPUnit_Framework_TestCase
{
	public function testAddHeader()
	{
		$request  = new Request(new Url('http://localhost'), 'GET', array('Accept-Encoding' => 'gzip'));
		$response = new Response();

		$filter = new GzipEncode();
		$filter->handle($request, $response, $this->getMockFilterChain($request, $response));

		$this->assertEquals('gzip', $response->getHeader('Content-Encoding'));
	}

	protected function getMockFilterChain($request, $response)
	{
		$filterChain = $this->getMockBuilder('PSX\Dispatch\FilterChain')
			->setConstructorArgs(array(array()))
			->setMethods(array('handle'))
			->getMock();

		$filterChain->expects($this->once())
			->method('handle')
			->with($this->equalTo($request), $this->equalTo($response));

		return $filterChain;
	}
}
