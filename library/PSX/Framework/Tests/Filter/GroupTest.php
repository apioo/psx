<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Framework\Tests\Filter;

use PSX\Framework\Filter\Group;
use PSX\Framework\Filter\FilterChain;
use PSX\Framework\Filter\FilterChainInterface;
use PSX\Framework\Filter\FilterInterface;
use PSX\Http\Request;
use PSX\Http\RequestInterface;
use PSX\Http\Response;
use PSX\Http\ResponseInterface;
use PSX\Uri\Url;

/**
 * GroupTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
