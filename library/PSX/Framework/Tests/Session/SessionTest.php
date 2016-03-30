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

namespace PSX\Framework\Tests\Session;

use PSX\Framework\Session\Session;

/**
 * SessionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PSX\Framework\Session\Session
     */
    protected $sess;

    protected function setUp()
    {
        $this->sess = new Session('psx_session');
    }

    protected function tearDown()
    {
        parent::tearDown();

        // we remove all values which were set during the test
        $_SESSION = [];
    }

    public function testGetSet()
    {
        $this->assertEquals(false, isset($_SESSION['foo']));
        $this->assertEquals(false, $this->sess->get('foo'));
        $this->assertEquals(false, $this->sess->has('foo'));

        $this->sess->set('foo', 'bar');

        $this->assertEquals(true, isset($_SESSION['foo']));
        $this->assertEquals('bar', $_SESSION['foo']);
        $this->assertEquals('bar', $this->sess->get('foo'));
        $this->assertEquals(true, $this->sess->has('foo'));
    }

    public function testPropertyGetSet()
    {
        $this->assertEquals(false, $this->sess->foo);

        $this->sess->foo = 'bar';

        $this->assertEquals('bar', $this->sess->foo);
    }

    public function testGetter()
    {
        $this->assertEquals('psx_session', $this->sess->getName());
        $this->assertEquals('PSX\Framework\Session\Session', $this->sess->getSessionTokenKey());

        // token is always the same since we are on CLI and have no user agent
        // or remote ip
        $this->assertEquals('876d2e7b380ea3c9567ef09df11c7926', $this->sess->getToken());
    }
}
