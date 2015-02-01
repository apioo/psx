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
use PSX\Dispatch\FilterChainInterface;
use PSX\Dispatch\FilterInterface;
use PSX\Http\Request;
use PSX\Http\RequestInterface;
use PSX\Http\Response;
use PSX\Http\ResponseInterface;
use PSX\Http\Stream\TempStream;
use PSX\Url;

/**
 * GroupTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class GroupTest extends \PHPUnit_Framework_TestCase
{
	public function testGroup()
	{
		$request  = new Request(new Url('http://localhost'), 'GET');
		$response = new Response();

		$subFilters[] = new DummyFilter(3);
		$subFilters[] = new DummyFilter(4);

		$filters[] = new DummyFilter(1);
		$filters[] = new DummyFilter(2);
		$filters[] = new Group($subFilters);
		$filters[] = new DummyFilter(5);
		$filters[] = new DummyFilter(6);

		$filterChain = new FilterChain($filters);
		$filterChain->handle($request, $response);

		$this->assertEquals(array(1, 2, 3, 4, 5, 6), DummyFilter::$calls);
	}
}

class DummyFilter implements FilterInterface
{
	public static $calls = array();

	protected $id;

	public function __construct($id)
	{
		$this->id = $id;
	}

	public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain)
	{
		self::$calls[] = $this->id;

		$filterChain->handle($request, $response);
	}
}
