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
use PSX\Data\RecordStoreInterface;
use PSX\Data\RecordStore\Memory;
use PSX\Dispatch\Filter\Exception\FailureException;
use PSX\Dispatch\Filter\Exception\MissingException;
use PSX\Dispatch\Filter\Exception\SuccessException;
use PSX\Http\Request;
use PSX\Http\Response;
use PSX\Http\Authentication;
use PSX\Url;

/**
 * DigestAccessAuthenticationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DigestAccessAuthenticationTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \PSX\Dispatch\Filter\Exception\SuccessException
	 */
	public function testSuccessful()
	{
		$store  = new Memory();
		$handle = $this->makeHandshake($store);

		$handle->onSuccess(function(){
			throw new SuccessException();
		});

		$username = 'test';
		$password = 'test';

		$nonce    = $store->load('digest')->getNonce();
		$opaque   = $store->load('digest')->getOpaque();
		$cnonce   = md5(uniqid());
		$nc       = '00000001';
		$ha1      = md5($username . ':psx:' . $password);
		$ha2      = md5('GET:/index.php');
		$response = md5($ha1 . ':' . $nonce . ':' . $nc . ':' . $cnonce . ':auth:' . $ha2);

		$params = array(
			'username' => $username,
			'realm'    => 'psx',
			'nonce'    => $nonce,
			'qop'      => 'auth',
			'nc'       => $nc,
			'cnonce'   => $cnonce,
			'response' => $response,
			'opaque'   => $opaque,
		);

		$request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => 'Digest ' . Authentication::encodeParameters($params)));
		$response = new Response();

		$handle->handle($request, $response);
	}

	/**
	 * @expectedException \PSX\Dispatch\Filter\Exception\FailureException
	 */
	public function testFailure()
	{
		$store  = new Memory();
		$handle = $this->makeHandshake($store);

		$handle->onFailure(function(){
			throw new FailureException();
		});

		$username = 'test';
		$password = 'bar';

		$nonce    = $store->load('digest')->getNonce();
		$opaque   = $store->load('digest')->getOpaque();
		$cnonce   = md5(uniqid());
		$nc       = '00000001';
		$ha1      = md5($username . ':psx:' . $password);
		$ha2      = md5('GET:/index.php');
		$response = md5($ha1 . ':' . $nonce . ':' . $nc . ':' . $cnonce . ':auth:' . $ha2);

		$params = array(
			'username' => $username,
			'realm'    => 'psx',
			'nonce'    => $nonce,
			'qop'      => 'auth',
			'nc'       => $nc,
			'cnonce'   => $cnonce,
			'response' => $response,
			'opaque'   => $opaque,
		);

		$request  = new Request(new Url('http://localhost/index.php'), 'GET', array('Authorization' => 'Digest ' . Authentication::encodeParameters($params)));
		$response = new Response();

		$handle->handle($request, $response);
	}

	public function testMissing()
	{
		$store  = new Memory();
		$handle = new DigestAccessAuthentication(function($username){
			return md5($username . ':psx:test');
		}, $store);

		$request  = new Request(new Url('http://localhost/index.php'), 'GET');
		$response = new Response();

		try
		{
			$handle->handle($request, $response);

			$this->fail('Must throw an Exception');
		}
		catch(\Exception $e)
		{
			$this->assertEquals(401, $response->getStatusCode());

			$header = (string) $response->getHeader('WWW-Authenticate');
			$params = Authentication::decodeParameters($header);

			$this->assertEquals('auth', $params['qop']);
			$this->assertTrue(strlen($params['nonce']) > 8);
			$this->assertTrue(strlen($params['opaque']) > 8);
		}
	}

	public function testMissingWrongType()
	{
		$store  = new Memory();
		$handle = new DigestAccessAuthentication(function($username){
			return md5($username . ':psx:test');
		}, $store);

		$request  = new Request(new Url('http://localhost'), 'GET', array('Authorization' => 'Foo'));
		$response = new Response();

		try
		{
			$handle->handle($request, $response);

			$this->fail('Must throw an Exception');
		}
		catch(\Exception $e)
		{
			$this->assertEquals(401, $response->getStatusCode());

			$header = (string) $response->getHeader('WWW-Authenticate');
			$params = Authentication::decodeParameters($header);

			$this->assertEquals('auth', $params['qop']);
			$this->assertTrue(strlen($params['nonce']) > 8);
			$this->assertTrue(strlen($params['opaque']) > 8);
		}
	}

	protected function makeHandshake(RecordStoreInterface $store)
	{
		// first we make an normal request without authentication then we should
		// get an 401 response with the nonce and opaque then we can make an
		// authentication request
		$handle = new DigestAccessAuthentication(function($username){
			return md5($username . ':psx:test');
		}, $store);

		$request  = new Request(new Url('http://localhost/index.php'), 'GET');
		$response = new Response();

		try
		{
			$handle->handle($request, $response);

			$this->fail('Must throw an Exception');
		}
		catch(\Exception $e)
		{
			$this->assertEquals(401, $response->getStatusCode());

			$header = (string) $response->getHeader('WWW-Authenticate');
			$params = Authentication::decodeParameters($header);

			$this->assertEquals('auth', $params['qop']);
			$this->assertTrue(strlen($params['nonce']) > 8);
			$this->assertTrue(strlen($params['opaque']) > 8);
		}

		// load digest from store
		$handle->loadDigest();

		return $handle;
	}
}
