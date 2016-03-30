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

namespace PSX\Framework\Tests\Dispatch;

use PSX\Framework\Config\Config;
use PSX\Framework\Dispatch\RequestFactory;

/**
 * The task of the request factory is to recreate the request from the server
 * environment vars. We assume the webserver follows rfc3875
 *
 * @see     http://www.ietf.org/rfc/rfc3875
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RequestFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $server;

    protected function setUp()
    {
        parent::setUp();

        // the test modifies the global server variable so store and reset the
        // values after the test
        $this->server = $_SERVER;
    }

    protected function tearDown()
    {
        parent::tearDown();

        $_SERVER = $this->server;
    }

    public function testCreateRequestNoPathAndNoDispatch()
    {
        $config = new Config(array(
            'psx_url'      => 'http://foo.com',
            'psx_dispatch' => '',
        ));

        $matrix = array(
            ['http://foo.com/', ['REQUEST_URI' => null]],
            ['http://foo.com/', ['REQUEST_URI' => '']],
            ['http://foo.com/', ['REQUEST_URI' => '/']],
            ['http://foo.com/bar', ['REQUEST_URI' => '/bar']],
            ['http://foo.com/bar?bar=test', ['REQUEST_URI' => '/bar?bar=test']],
            ['http://foo.com/bar/?bar=test', ['REQUEST_URI' => '/bar/?bar=test']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/?bar=test']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '?bar=test']],
        );

        foreach ($matrix as $data) {
            list($uri, $env) = $data;

            $request = $this->getRequest($env, $config);

            $this->assertEquals($uri, (string) $request->getUri(), var_export($env, true));
        }

        $this->assertCorrectRequestUriWorks($config);
    }

    public function testCreateRequestNoProtocolNoPathAndNoDispatch()
    {
        $config = new Config(array(
            'psx_url'      => '//foo.com',
            'psx_dispatch' => '',
        ));

        $matrix = array(
            ['http://foo.com/', ['REQUEST_URI' => null]],
            ['http://foo.com/', ['REQUEST_URI' => '']],
            ['http://foo.com/', ['REQUEST_URI' => '/']],
            ['http://foo.com/bar', ['REQUEST_URI' => '/bar']],
            ['http://foo.com/bar?bar=test', ['REQUEST_URI' => '/bar?bar=test']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/?bar=test']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '?bar=test']],
        );

        foreach ($matrix as $data) {
            list($uri, $env) = $data;

            $request = $this->getRequest($env, $config);

            $this->assertEquals($uri, (string) $request->getUri(), var_export($env, true));
        }

        $this->assertCorrectRequestUriWorks($config);
    }

    public function testCreateRequestNoPathAndDispatch()
    {
        $config = new Config(array(
            'psx_url'      => 'http://foo.com',
            'psx_dispatch' => 'index.php/',
        ));

        $matrix = array(
            ['http://foo.com/', ['REQUEST_URI' => null]],
            ['http://foo.com/', ['REQUEST_URI' => '']],
            ['http://foo.com/', ['REQUEST_URI' => '/']],
            ['http://foo.com/', ['REQUEST_URI' => '/index.php']],
            ['http://foo.com/', ['REQUEST_URI' => '/index.php/']],
            ['http://foo.com/bar', ['REQUEST_URI' => '/index.php/bar']],
            ['http://foo.com/bar?bar=test', ['REQUEST_URI' => '/index.php/bar?bar=test']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/index.php/?bar=test']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/index.php?bar=test']],
        );

        foreach ($matrix as $data) {
            list($uri, $env) = $data;

            $request = $this->getRequest($env, $config);

            $this->assertEquals($uri, (string) $request->getUri(), var_export($env, true));
        }

        $this->assertCorrectRequestUriWorks($config);
    }

    public function testCreateRequestPathAndNoDispatch()
    {
        $config = new Config(array(
            'psx_url'      => 'http://foo.com/sub/folder',
            'psx_dispatch' => '',
        ));

        $matrix = array(
            ['http://foo.com/', ['REQUEST_URI' => null]],
            ['http://foo.com/', ['REQUEST_URI' => '/sub']],
            ['http://foo.com/', ['REQUEST_URI' => '/sub/']],
            ['http://foo.com/', ['REQUEST_URI' => '/sub/folder']],
            ['http://foo.com/', ['REQUEST_URI' => '/sub/folder/']],
            ['http://foo.com/bar', ['REQUEST_URI' => '/sub/folder/bar']],
            ['http://foo.com/bar/', ['REQUEST_URI' => '/sub/folder/bar/']],
            ['http://foo.com/bar/?bar=test', ['REQUEST_URI' => '/sub/folder/bar/?bar=test']],
            ['http://foo.com/bar?bar=test', ['REQUEST_URI' => '/sub/folder/bar?bar=test']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/sub/folder/?bar=test']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/sub/folder?bar=test']],
        );

        foreach ($matrix as $data) {
            list($uri, $env) = $data;

            $request = $this->getRequest($env, $config);

            $this->assertEquals($uri, (string) $request->getUri(), var_export($env, true));
        }

        $this->assertCorrectRequestUriWorks($config);
    }

    public function testCreateRequestPathAndDispatch()
    {
        $config = new Config(array(
            'psx_url'      => 'http://foo.com/sub/folder',
            'psx_dispatch' => 'index.php/',
        ));

        $matrix = array(
            ['http://foo.com/', ['REQUEST_URI' => null]],
            ['http://foo.com/', ['REQUEST_URI' => '/sub']],
            ['http://foo.com/', ['REQUEST_URI' => '/sub/']],
            ['http://foo.com/', ['REQUEST_URI' => '/sub/folder']],
            ['http://foo.com/', ['REQUEST_URI' => '/sub/folder/']],
            ['http://foo.com/', ['REQUEST_URI' => '/sub/folder/index.php']],
            ['http://foo.com/', ['REQUEST_URI' => '/sub/folder/index.php/']],
            ['http://foo.com/bar', ['REQUEST_URI' => '/sub/folder/index.php/bar']],
            ['http://foo.com/bar/', ['REQUEST_URI' => '/sub/folder/index.php/bar/']],
            ['http://foo.com/bar?bar=test', ['REQUEST_URI' => '/sub/folder/index.php/bar?bar=test']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/sub/folder/index.php/?bar=test']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/sub/folder/index.php?bar=test']],
        );

        foreach ($matrix as $data) {
            list($uri, $env) = $data;

            $request = $this->getRequest($env, $config);

            $this->assertEquals($uri, (string) $request->getUri(), var_export($env, true));
        }

        $this->assertCorrectRequestUriWorks($config);
    }

    public function testCreateRequestNoProtocol()
    {
        $config = new Config(array(
            'psx_url'      => '//foo.com',
            'psx_dispatch' => '',
        ));

        $matrix = array(
            ['http://foo.com/', []],
            ['http://foo.com/', ['HTTPS' => '']],
            ['http://foo.com/', ['HTTPS' => '0']],
            ['https://foo.com/', ['HTTPS' => '1']],
            ['https://foo.com/', ['HTTPS' => 'on']],
            ['http://foo.com/', ['HTTPS' => 'off']],
            ['https://foo.com/', ['HTTPS' => 'ON']],
            ['http://foo.com/', ['HTTPS' => 'OFF']],
        );

        foreach ($matrix as $data) {
            list($uri, $env) = $data;

            $request = $this->getRequest($env, $config);

            $this->assertEquals($uri, (string) $request->getUri(), var_export($env, true));
        }
    }

    /**
     * This ensures that if an correct request uri arrives at our application we
     * get the correct uri even if we have setup an dispatch or path segment
     * in the url
     */
    public function assertCorrectRequestUriWorks($config)
    {
        $matrix = array(
            ['http://foo.com/', ['REQUEST_URI' => null]],
            ['http://foo.com/', ['REQUEST_URI' => '']],
            ['http://foo.com/', ['REQUEST_URI' => '/']],
            ['http://foo.com/bar', ['REQUEST_URI' => '/bar']],
            ['http://foo.com/bar/', ['REQUEST_URI' => '/bar/']],
            ['http://foo.com/bar?bar=test', ['REQUEST_URI' => '/bar?bar=test']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '/?bar=test']],
            ['http://foo.com/?bar=test', ['REQUEST_URI' => '?bar=test']],
        );

        foreach ($matrix as $data) {
            list($uri, $env) = $data;

            $request = $this->getRequest($env, $config);

            $this->assertEquals($uri, (string) $request->getUri(), var_export($env, true));
        }
    }

    public function testCreateRequestInCli()
    {
        $config = new Config(array(
            'psx_url' => 'http://foo.com',
        ));

        $factory = $this->getMockBuilder('PSX\Framework\Dispatch\RequestFactory')
            ->setConstructorArgs(array($config))
            ->setMethods(array('isCli'))
            ->getMock();

        $factory->expects($this->once())
            ->method('isCli')
            ->will($this->returnValue(true));

        $_SERVER['argv'][1] = '/foo';

        $this->assertEquals('http://foo.com/foo', (string) $factory->createRequest()->getUri());
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testCreateRequestInvalidUrl()
    {
        $config = new Config(array(
            'psx_url' => 'foobar',
        ));

        $factory = new RequestFactory($config);
        $factory->createRequest();
    }

    public function testGetRequestMethod()
    {
        $config = new Config(array(
            'psx_url' => 'http://foo.com',
        ));

        $env = array('REQUEST_METHOD' => 'POST');

        $_SERVER['argv'][1] = '/';

        $request = $this->getRequest($env, $config, true);

        $this->assertEquals('POST', $request->getMethod());
    }

    public function testGetRequestMethodOverwrite()
    {
        $config = new Config(array(
            'psx_url' => 'http://foo.com',
        ));

        $env = array('REQUEST_METHOD' => 'POST', 'HTTP_X_HTTP_METHOD_OVERRIDE' => 'PUT');

        $_SERVER['argv'][1] = '/';

        $request = $this->getRequest($env, $config, true);

        $this->assertEquals('PUT', $request->getMethod());
    }

    public function testGetRequestMethodOverwriteInvalid()
    {
        $config = new Config(array(
            'psx_url' => 'http://foo.com',
        ));

        $env = array('REQUEST_METHOD' => 'POST', 'HTTP_X_HTTP_METHOD_OVERRIDE' => 'FOO');

        $_SERVER['argv'][1] = '/';

        $request = $this->getRequest($env, $config, true);

        $this->assertEquals('POST', $request->getMethod());
    }

    public function testGetRequestHeader()
    {
        $config = new Config(array(
            'psx_url' => 'http://foo.com',
        ));

        $env = array('HTTP_FOO_BAR' => 'foobar');

        $_SERVER['argv'][1] = '/';

        $request = $this->getRequest($env, $config, true);

        $this->assertEquals('foobar', $request->getHeader('Foo-Bar'));
    }

    public function testGetRequestHeaderContentHeader()
    {
        $config = new Config(array(
            'psx_url' => 'http://foo.com',
        ));

        $env = array('HTTP_FOO_BAR' => 'foobar', 'CONTENT_LENGTH' => 8, 'CONTENT_MD5' => 'foobar', 'CONTENT_TYPE' => 'text/html');

        $_SERVER['argv'][1] = '/';

        $request = $this->getRequest($env, $config, true);

        $this->assertEquals('foobar', $request->getHeader('Foo-Bar'));
        $this->assertEquals(8, $request->getHeader('Content-Length'));
        $this->assertEquals('foobar', $request->getHeader('Content-MD5'));
        $this->assertEquals('text/html', $request->getHeader('Content-Type'));
    }

    public function testGetRequestHeaderRedirectAuthorizationHeader()
    {
        $config = new Config(array(
            'psx_url' => 'http://foo.com',
        ));

        $env = array('HTTP_FOO_BAR' => 'foobar', 'REDIRECT_HTTP_AUTHORIZATION' => 'Basic Zm9vOmJhcg==');

        $_SERVER['argv'][1] = '/';

        $request = $this->getRequest($env, $config, true);

        $this->assertEquals('foobar', $request->getHeader('Foo-Bar'));
        $this->assertEquals('Basic Zm9vOmJhcg==', $request->getHeader('Authorization'));
    }

    public function testGetRequestHeaderPhpAuthUser()
    {
        $config = new Config(array(
            'psx_url' => 'http://foo.com',
        ));

        $env = array('HTTP_FOO_BAR' => 'foobar', 'PHP_AUTH_USER' => 'foo', 'PHP_AUTH_PW' => 'bar');

        $_SERVER['argv'][1] = '/';

        $request = $this->getRequest($env, $config, true);

        $this->assertEquals('foobar', $request->getHeader('Foo-Bar'));
        $this->assertEquals('Basic Zm9vOmJhcg==', $request->getHeader('Authorization'));
    }

    public function testGetRequestHeaderPhpAuthUserNoPw()
    {
        $config = new Config(array(
            'psx_url' => 'http://foo.com',
        ));

        $env = array('HTTP_FOO_BAR' => 'foobar', 'PHP_AUTH_USER' => 'foo', 'PHP_AUTH_PW' => null);

        $_SERVER['argv'][1] = '/';

        $request = $this->getRequest($env, $config, true);

        $this->assertEquals('foobar', $request->getHeader('Foo-Bar'));
        $this->assertEquals('Basic Zm9vOg==', $request->getHeader('Authorization'));
    }

    public function testGetRequestHeaderDigest()
    {
        $config = new Config(array(
            'psx_url' => 'http://foo.com',
        ));

        $env = array('HTTP_FOO_BAR' => 'foobar', 'PHP_AUTH_DIGEST' => 'Digest foobar');

        $_SERVER['argv'][1] = '/';

        $request = $this->getRequest($env, $config, true);

        $this->assertEquals('foobar', $request->getHeader('Foo-Bar'));
        $this->assertEquals('Digest foobar', $request->getHeader('Authorization'));
    }

    protected function getRequest(array $env, Config $config, $isCli = false)
    {
        $factory = $this->getMockBuilder('PSX\Framework\Dispatch\RequestFactory')
            ->setConstructorArgs(array($config))
            ->setMethods(array('isCli'))
            ->getMock();

        $factory->expects($this->once())
            ->method('isCli')
            ->will($this->returnValue($isCli));

        foreach ($env as $key => $value) {
            $_SERVER[$key] = $value;
        }

        return $factory->createRequest();
    }
}
