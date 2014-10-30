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
use PSX\Http\Exception\BadRequestException;
use PSX\Http\Exception\UnauthorizedException;
use PSX\Oauth;
use PSX\Oauth\Provider\Data\Consumer;
use PSX\Url;

/**
 * OauthAuthenticationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class OauthAuthenticationTest extends \PHPUnit_Framework_TestCase
{
	const CONSUMER_KEY    = 'dpf43f3p2l4k3l03';
	const CONSUMER_SECRET = 'kd94hf93k423kf44';
	const TOKEN           = 'hh5s93j4hdidpola';
	const TOKEN_SECRET    = 'hdhd0244k9j7ao03';

	/**
	 * @expectedException PSX\Dispatch\Filter\Exception\SuccessException
	 */
	public function testSuccessful()
	{
		$handle = new OauthAuthentication(function($consumerKey, $token){

			if($consumerKey == self::CONSUMER_KEY && $token == self::TOKEN)
			{
				return new Consumer(self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);
			}

		});

		$handle->onSuccess(function(){
			throw new SuccessException();
		});

		$oauth = new Oauth(new Http());
		$value = $oauth->getAuthorizationHeader(new Url('http://localhost/index.php'), self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);

		$request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => $value));
		$response = new Response();

		$handle->handle($request, $response);
	}

	/**
	 * @expectedException PSX\Http\Exception\BadRequestException
	 */
	public function testFailureEmptyCredentials()
	{
		$handle = new OauthAuthentication(function($consumerKey, $token){

			if($consumerKey == self::CONSUMER_KEY && $token == self::TOKEN)
			{
				return new Consumer(self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);
			}

		});

		$oauth = new Oauth(new Http());
		$value = $oauth->getAuthorizationHeader(new Url('http://localhost/index.php'), '', '', '', '');

		$request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => $value));
		$response = new Response();

		$handle->handle($request, $response);
	}

	/**
	 * @expectedException PSX\Http\Exception\BadRequestException
	 */
	public function testFailureWrongConsumerKey()
	{
		$handle = new OauthAuthentication(function($consumerKey, $token){

			if($consumerKey == self::CONSUMER_KEY && $token == self::TOKEN)
			{
				return new Consumer(self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);
			}

		});

		$oauth = new Oauth(new Http());
		$value = $oauth->getAuthorizationHeader(new Url('http://localhost/index.php'), 'foobar', self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);

		$request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => $value));
		$response = new Response();

		$handle->handle($request, $response);
	}

	/**
	 * @expectedException PSX\Http\Exception\BadRequestException
	 */
	public function testFailureWrongConsumerSecret()
	{
		$handle = new OauthAuthentication(function($consumerKey, $token){

			if($consumerKey == self::CONSUMER_KEY && $token == self::TOKEN)
			{
				return new Consumer(self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);
			}

		});

		$oauth = new Oauth(new Http());
		$value = $oauth->getAuthorizationHeader(new Url('http://localhost/index.php'), self::CONSUMER_KEY, 'foobar', self::TOKEN, self::TOKEN_SECRET);

		$request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => $value));
		$response = new Response();

		$handle->handle($request, $response);
	}

	/**
	 * @expectedException PSX\Http\Exception\BadRequestException
	 */
	public function testFailureWrongToken()
	{
		$handle = new OauthAuthentication(function($consumerKey, $token){

			if($consumerKey == self::CONSUMER_KEY && $token == self::TOKEN)
			{
				return new Consumer(self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);
			}

		});

		$oauth = new Oauth(new Http());
		$value = $oauth->getAuthorizationHeader(new Url('http://localhost/index.php'), self::CONSUMER_KEY, self::CONSUMER_SECRET, 'foobar', self::TOKEN_SECRET);

		$request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => $value));
		$response = new Response();

		$handle->handle($request, $response);
	}

	/**
	 * @expectedException PSX\Http\Exception\BadRequestException
	 */
	public function testFailureWrongTokenSecret()
	{
		$handle = new OauthAuthentication(function($consumerKey, $token){

			if($consumerKey == self::CONSUMER_KEY && $token == self::TOKEN)
			{
				return new Consumer(self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);
			}

		});

		$oauth = new Oauth(new Http());
		$value = $oauth->getAuthorizationHeader(new Url('http://localhost/index.php'), self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, 'foobar');

		$request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => $value));
		$response = new Response();

		$handle->handle($request, $response);
	}

	public function testMissing()
	{
		$handle = new OauthAuthentication(function($consumerKey, $token){

			if($consumerKey == self::CONSUMER_KEY && $token == self::TOKEN)
			{
				return new Consumer(self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);
			}

		});

		$oauth = new Oauth(new Http());
		$value = $oauth->getAuthorizationHeader(new Url('http://localhost/index.php'), self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);

		$request  = new Request(new Url('http://localhost/index.php'), 'GET');
		$response = new Response();

		try
		{
			$handle->handle($request, $response);

			$this->fail('Must throw an Exception');
		}
		catch(UnauthorizedException $e)
		{
			$this->assertEquals(401, $e->getStatusCode());
			$this->assertEquals('Oauth', $e->getType());
			$this->assertEquals(array('realm' => 'psx'), $e->getParameters());
		}
	}

	public function testMissingWrongType()
	{
		$handle = new OauthAuthentication(function($consumerKey, $token){

			if($consumerKey == self::CONSUMER_KEY && $token == self::TOKEN)
			{
				return new Consumer(self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);
			}
			
		});

		$oauth = new Oauth(new Http());
		$value = $oauth->getAuthorizationHeader(new Url('http://localhost/index.php'), self::CONSUMER_KEY, self::CONSUMER_SECRET, self::TOKEN, self::TOKEN_SECRET);

		$request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => 'Foo'));
		$response = new Response();

		try
		{
			$handle->handle($request, $response);

			$this->fail('Must throw an Exception');
		}
		catch(UnauthorizedException $e)
		{
			$this->assertEquals(401, $e->getStatusCode());
			$this->assertEquals('Oauth', $e->getType());
			$this->assertEquals(array('realm' => 'psx'), $e->getParameters());
		}
	}
}
