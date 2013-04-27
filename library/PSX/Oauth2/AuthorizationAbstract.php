<?php
/*
 *  $Id: AuthorizationAbstract.php 662 2012-10-07 16:45:03Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Oauth2;

use PSX\Data\ReaderFactory;
use PSX\Data\ReaderInterface;
use PSX\Data\Reader;
use PSX\Exception;
use PSX\Http;
use PSX\Http\PostRequest;
use PSX\Json;
use PSX\Url;

/**
 * PSX_Oauth2_AuthorizationAbstract
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Oauth2
 * @version    $Revision: 662 $
 */
abstract class AuthorizationAbstract
{
	const AUTH_BASIC = 0x1;
	const AUTH_POST  = 0x2;

	protected $http;
	protected $url;

	protected $clientId;
	protected $clientSecret;
	protected $type;

	public function __construct(Http $http, Url $url)
	{
		$this->http = $http;
		$this->url  = $url;
	}

	public function setClientPassword($clientId, $clientSecret, $type = 0x1)
	{
		$this->clientId     = $clientId;
		$this->clientSecret = $clientSecret;
		$this->type         = $type;
	}

	/**
	 * Tries to refresh an access token if an refresh token is available.
	 * Returns the new received access token or throws an excepion
	 *
	 * @return PSX_Oauth2_AccessToken
	 */
	public function refreshToken(AccessToken $accessToken)
	{
		// request data
		$refreshToken = $accessToken->getRefreshToken();
		$scope        = $accessToken->getScope();

		if(empty($refreshToken))
		{
			throw new Exception('No refresh token was set');
		}

		$data = array(

			'grant_type'    => 'refresh_token',
			'refresh_token' => $refreshToken,

		);

		if(!empty($scope))
		{
			$data['scope'] = $scope;
		}

		// authentication
		$header = array();

		if($this->type == self::AUTH_BASIC)
		{
			$header['Authorization'] = 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret);
		}

		if($this->type == self::AUTH_POST)
		{
			$data['client_id']     = $this->clientId;
			$data['client_secret'] = $this->clientSecret;
		}

		$request  = new PostRequest($this->url, $header, $data);
		$response = $this->http->request($request);

		if($response->getCode() == 200)
		{
			$reader = new Reader\Json();
			$result = $reader->read($response);

			$accessToken = new AccessToken();
			$accessToken->import($result);

			return $accessToken;
		}
		else
		{
			throw new Exception('Could not refresh access token');
		}
	}

	protected function request(array $header, $data)
	{
		$request  = new PostRequest($this->url, $header, $data);
		$response = $this->http->request($request);

		if($response->getCode() == 200)
		{
			// if the content type is application/json use json reader else
			// asume application/x-www-form-urlencoded 
			if(strpos($response->getHeader('Content-Type'), 'application/json') !== false)
			{
				$type = ReaderInterface::JSON;
			}
			else
			{
				$type = ReaderInterface::FORM;
			}

			// parse response
			$reader = ReaderFactory::getReader($type);
			$result = $reader->read($response);

			// import data
			$accessToken = new AccessToken();
			$accessToken->import($result);

			return $accessToken;
		}
		else
		{
			$resp = Json::decode($response->getBody());

			self::throwErrorException($resp);
		}
	}

	/**
	 * Each class wich extends PSX_Oauth2_Authorization should have the method
	 * getAccessToken(). Since the method can have different arguments we can
	 * not declare the method as abstract but it will stay here for reference
	 *
	 * @return PSX_Oauth2_AccessToken
	 */
	//abstract public function getAccessToken();

	/**
	 * Parses the $data array for an error response and throws the most fitting
	 * exception including also the error message and url if available
	 *
	 * @throws PSX_Oauth2_Authorization_Exception
	 */
	public static function throwErrorException($data)
	{
		// we do not type hint as array because we want throw a clean exception
		// in case data is not an array
		if(!is_array($data))
		{
			throw new Exception('Invalid response');
		}

		// unfortunatly facebook doesnt follow the oauth draft 26 and set in the 
		// response error key the correct error string instead the error key 
		// contains an object with the type and message. Temporary we will use 
		// this hack since the spec is not an rfc. If the rfc is released we 
		// will strictly follow the spec and remove this hack hopefully facebook 
		// too
		if(is_array($data['error']) && isset($data['error']['type']) && isset($data['error']['message']))
		{
			$data['error_description'] = $data['error']['message'];
			$data['error'] = 'invalid_request';
		}

		$error = isset($data['error']) ? strtolower($data['error']) : null;
		$desc  = isset($data['error_description']) ? htmlspecialchars($data['error_description']) : null;
		$uri   = isset($data['error_uri']) ? $data['error_uri'] : null;

		if(in_array($error, array('invalid_request', 'invalid_client', 'invalid_grant', 'unauthorized_client', 'unsupported_grant_type', 'invalid_scope')))
		{
			$exceptionClass = '\PSX\Oauth2\Authorization\Exception\\' . implode('', array_map('ucfirst', explode('_', $error))) . 'Exception';
			$message        = '';

			if(!empty($desc))
			{
				$message.= strlen($desc) > 128 ? substr($desc, 0, 125) . '...' : $desc;
			}

			if(!empty($uri) && filter_var($uri, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED))
			{
				$message.= ' More informations at ' . $uri;
			}

			if(!empty($message))
			{
				throw new $exceptionClass($message);
			}
			else
			{
				throw new $exceptionClass('No message available');
			}
		}
		else
		{
			throw new Exception('Invalid error type');
		}
	}
}

