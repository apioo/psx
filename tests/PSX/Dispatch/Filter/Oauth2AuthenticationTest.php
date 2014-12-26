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

namespace PSX\Dispatch\Filter;

use Closure;
use PSX\Dispatch\Filter\Exception\FailureException;
use PSX\Dispatch\Filter\Exception\MissingException;
use PSX\Dispatch\Filter\Exception\SuccessException;
use PSX\Http;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Authentication;
use PSX\Http\Exception\UnauthorizedException;
use PSX\Oauth2;
use PSX\Oauth2\AccessToken;
use PSX\Oauth2\Token\Bearer;
use PSX\Url;

/**
 * Oauth2AuthenticationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Oauth2AuthenticationTest extends \PHPUnit_Framework_TestCase
{
	const ACCESS_TOKEN = '2YotnFZFEjr1zCsicMWpAA';

	public function testSuccessful()
	{
		$handle = new Oauth2Authentication(function($accessToken){

			return $accessToken == self::ACCESS_TOKEN;

		});

		$handle->onSuccess(function(){
			// success
		});

		$oauth = new Oauth2();
		$value = $oauth->getAuthorizationHeader($this->getAccessToken());

		$request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => $value));
		$response = new Response();

		$filterChain = $this->getMockFilterChain();
		$filterChain->expects($this->once())
			->method('handle')
			->with($this->equalTo($request), $this->equalTo($response));

		$handle->handle($request, $response, $filterChain);
	}

	/**
	 * @expectedException PSX\Http\Exception\BadRequestException
	 */
	public function testFailure()
	{
		$handle = new Oauth2Authentication(function($accessToken){

			return false;

		});

		$oauth = new Oauth2();
		$value = $oauth->getAuthorizationHeader($this->getAccessToken());

		$request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => $value));
		$response = new Response();

		$filterChain = $this->getMockFilterChain();
		$filterChain->expects($this->never())
			->method('handle');

		$handle->handle($request, $response, $filterChain);
	}

	/**
	 * @expectedException PSX\Http\Exception\UnauthorizedException
	 */
	public function testFailureEmptyCredentials()
	{
		$handle = new Oauth2Authentication(function($accessToken){
			
			return $accessToken == self::ACCESS_TOKEN;

		});

		$oauth = new Oauth2();
		$value = '';

		$request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => $value));
		$response = new Response();

		$filterChain = $this->getMockFilterChain();
		$filterChain->expects($this->never())
			->method('handle');

		$handle->handle($request, $response, $filterChain);
	}

	public function testMissing()
	{
		$handle = new Oauth2Authentication(function($accessToken){
			
			return $accessToken == self::ACCESS_TOKEN;

		});

		$oauth = new Oauth2();
		$value = $oauth->getAuthorizationHeader($this->getAccessToken());

		$request  = new Request(new Url('http://localhost/index.php'), 'GET');
		$response = new Response();

		$filterChain = $this->getMockFilterChain();
		$filterChain->expects($this->never())
			->method('handle');

		try
		{
			$handle->handle($request, $response, $filterChain);

			$this->fail('Must throw an Exception');
		}
		catch(UnauthorizedException $e)
		{
			$this->assertEquals(401, $e->getStatusCode());
			$this->assertEquals('Bearer', $e->getType());
			$this->assertEquals(array('realm' => 'psx'), $e->getParameters());
		}
	}

	public function testMissingWrongType()
	{
		$handle = new Oauth2Authentication(function($accessToken){
			
			return $accessToken == self::ACCESS_TOKEN;

		});

		$oauth = new Oauth2();
		$value = $oauth->getAuthorizationHeader($this->getAccessToken());

		$request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => 'Foo'));
		$response = new Response();

		$filterChain = $this->getMockFilterChain();
		$filterChain->expects($this->never())
			->method('handle');

		try
		{
			$handle->handle($request, $response, $filterChain);

			$this->fail('Must throw an Exception');
		}
		catch(UnauthorizedException $e)
		{
			$this->assertEquals(401, $e->getStatusCode());
			$this->assertEquals('Bearer', $e->getType());
			$this->assertEquals(array('realm' => 'psx'), $e->getParameters());
		}
	}

	protected function getAccessToken()
	{
		$accessToken = new AccessToken();
		$accessToken->setAccessToken('2YotnFZFEjr1zCsicMWpAA');
		$accessToken->setTokenType('bearer');
		$accessToken->setExpiresIn(3600);
		$accessToken->setRefreshToken('tGzv3JOkF0XG5Qx2TlKWIA');

		return $accessToken;
	}

	protected function getMockFilterChain()
	{
		return $this->getMockBuilder('PSX\Dispatch\FilterChain')
			->setConstructorArgs(array(array()))
			->setMethods(array('handle'))
			->getMock();
	}
}
