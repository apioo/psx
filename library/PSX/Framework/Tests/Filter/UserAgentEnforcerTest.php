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

use PSX\Framework\Filter\UserAgentEnforcer;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Uri\Url;

/**
 * UserAgentEnforcerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class UserAgentEnforcerTest extends \PHPUnit_Framework_TestCase
{
    public function testUserAgent()
    {
        $request  = new Request(new Url('http://localhost'), 'GET', array('User-Agent' => 'foobar'));
        $response = new Response();

        $filterChain = $this->getMockBuilder('PSX\Framework\Filter\FilterChain')
            ->setConstructorArgs(array(array()))
            ->setMethods(array('handle'))
            ->getMock();

        $filterChain->expects($this->once())
            ->method('handle')
            ->with($this->equalTo($request), $this->equalTo($response));

        $filter = new UserAgentEnforcer();
        $filter->handle($request, $response, $filterChain);
    }

    /**
     * @expectedException \PSX\Http\Exception\BadRequestException
     */
    public function testNoUserAgent()
    {
        $request  = new Request(new Url('http://localhost'), 'GET');
        $response = new Response();

        $filterChain = $this->getMockBuilder('PSX\Framework\Filter\FilterChain')
            ->setConstructorArgs(array(array()))
            ->setMethods(array('handle'))
            ->getMock();

        $filterChain->expects($this->never())
            ->method('handle');

        $filter = new UserAgentEnforcer();
        $filter->handle($request, $response, $filterChain);
    }
}
