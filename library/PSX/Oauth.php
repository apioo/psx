<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX;

use PSX\Data\Reader\Form;
use PSX\Http\PostRequest;
use PSX\Oauth\Provider\Data\Response;
use PSX\Oauth\Provider\Data\ResponseImporter;
use PSX\Oauth\Signature;

/**
 * This is a consumer implementation of OAuth Core 1.0
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @author  Andy Smith <http://term.ie>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://tools.ietf.org/html/rfc5849
 */
class Oauth
{
	private $requestMethod;
	private $url;
	private $params;
	private $baseString;

	private $code;
	private $lastError;
	private $http;

	public function __construct(Http $http)
	{
		$this->http = $http;
	}

	/**
	 * Requests a new "request token" from the $url using the consumer key and
	 * secret. The $url must be valid request token endpoint. Returns an array
	 * with all key values pairs from the response i.e.
	 * <code>
	 * $response = $oauth->requestToken(...);
	 *
	 * $token       = $response->getToken();
	 * $tokenSecret = $response->getTokenSecret();
	 * </code>
	 *
	 * @see http://oauth.net/core/1.0a#rfc.section.6.1
	 * @param PSX\Url $url
	 * @param string $consumerKey
	 * @param string $consumerSecret
	 * @param string $method
	 * @return PSX\Oauth\Provider\Data\Response
	 */
	public function requestToken(Url $url, $consumerKey, $consumerSecret, $method = 'HMAC-SHA1', $callback = false)
	{
		$values = array(
			'oauth_consumer_key'     => $consumerKey,
			'oauth_signature_method' => $method,
			'oauth_timestamp'        => self::getTimestamp(),
			'oauth_nonce'            => self::getNonce(),
			'oauth_version'          => self::getVersion(),
		);

		// if we have an callback add them to the request
		if(!empty($callback))
		{
			$values['oauth_callback'] = $callback;
		}
		else
		{
			$values['oauth_callback'] = 'oob';
		}

		// build the base string
		$this->requestMethod = 'POST';
		$this->url           = $url;
		$this->params        = array_merge($values, $url->getParams());

		$this->baseString    = self::buildBasestring($this->requestMethod, $this->url, $this->params);

		// get the signature object
		$signature = self::getSignature($method);

		// generate the signature
		$values['oauth_signature'] = $signature->build($this->baseString, $consumerSecret);

		// request unauthorized token
		$request   = new PostRequest($url, array(
			'Authorization' => 'OAuth realm="psx", ' . self::buildAuthString($values),
			'User-Agent'    => __CLASS__ . ' ' . Base::VERSION,
		));

		$response  = $this->http->request($request);
		$lastError = $this->http->getLastError();

		if(empty($lastError))
		{
			$this->code      = $response->getCode();
			$this->lastError = false;

			// parse the response
			$reader   = new Form();
			$record   = new Response();
			$importer = new ResponseImporter();
			$importer->import($record, $reader->read($response));

			return $record;
		}
		else
		{
			$this->code      = false;
			$this->lastError = $lastError;

			return false;
		}
	}

	/**
	 * Redirects the user to the $url. The $url must be a valid user
	 * authorization endpoint. All values in $params are added to the url as
	 * GET vars.
	 *
	 * @param PSX\Url $url
	 * @param array $params
	 * @return void
	 */
	public function userAuthorization(Url $url, array $params = array())
	{
		$url->addParams($params);

		header('Location: ' . strval($url));
		exit;
	}

	/**
	 * Exchange an request token with an access token. We receive the "token"
	 * and "verifier" from the service provider wich redirects the user to the
	 * callback in this redirect are the $token and $verifier. Returns an access
	 * token and secret i.e.
	 * <code>
	 * $response = $oauth->accessToken(...);
	 *
	 * $token       = $response->getToken();
	 * $tokenSecret = $response->getTokenSecret();
	 * </code>
	 *
	 * @see http://oauth.net/core/1.0a#rfc.section.6.3
	 * @param PSX\Url $url
	 * @param string $consumerKey
	 * @param string $consumerSecret
	 * @param string $token
	 * @param string $tokenSecret
	 * @param string $verifier
	 * @param string $method
	 * @return PSX\Oauth\Provider\Data\Response
	 */
	public function accessToken(Url $url, $consumerKey, $consumerSecret, $token, $tokenSecret, $verifier, $method = 'HMAC-SHA1')
	{
		$values = array(
			'oauth_consumer_key'     => $consumerKey,
			'oauth_token'            => $token,
			'oauth_signature_method' => $method,
			'oauth_timestamp'        => self::getTimestamp(),
			'oauth_nonce'            => self::getNonce(),
			'oauth_version'          => self::getVersion(),
			'oauth_verifier'         => $verifier,
		);

		// build the base string
		$this->requestMethod = 'POST';
		$this->url           = $url;
		$this->params        = array_merge($values, $url->getParams());

		$this->baseString    = self::buildBasestring($this->requestMethod, $this->url, $this->params);

		// get the signature object
		$signature = self::getSignature($method);

		// generate the signature
		$values['oauth_signature'] = $signature->build($this->baseString, $consumerSecret, $tokenSecret);

		// request access token
		$request   = new PostRequest($url, array(
			'Authorization' => 'OAuth realm="psx", ' . self::buildAuthString($values),
			'User-Agent'    => __CLASS__ . ' ' . Base::VERSION,
		));

		$response  = $this->http->request($request);
		$lastError = $this->http->getLastError();

		if(empty($lastError))
		{
			$this->code      = $response->getCode();
			$this->lastError = false;

			// parse the response
			$reader   = new Form();
			$record   = new Response();
			$importer = new ResponseImporter();
			$importer->import($record, $reader->read($response));

			return $record;
		}
		else
		{
			$this->code      = false;
			$this->lastError = $lastError;

			return false;
		}
	}

	/**
	 * If you have established a token and token secret you can use this method
	 * to get the authorization header. You can add the header to an http
	 * request to make an valid oauth request i.e.
	 * <code>
	 * $header = array(
	 * 	'Authorization: ' . $oauth->getAuthorizationHeader(...),
	 * );
	 * </code>
	 *
	 * @param PSX\Url $url
	 * @param string $consumerKey
	 * @param string $consumerSecret
	 * @param string $token
	 * @param string $tokenSecret
	 * @param string $method
	 * @param string $requestMethod
	 * @return string
	 */
	public function getAuthorizationHeader(Url $url, $consumerKey, $consumerSecret, $token, $tokenSecret, $method = 'HMAC-SHA1', $requestMethod = 'GET')
	{
		$values = array(
			'oauth_consumer_key'     => $consumerKey,
			'oauth_token'            => $token,
			'oauth_signature_method' => $method,
			'oauth_timestamp'        => self::getTimestamp(),
			'oauth_nonce'            => self::getNonce(),
			'oauth_version'          => self::getVersion(),
		);

		// build the base string
		$this->requestMethod = $requestMethod;
		$this->url           = $url;
		$this->params        = array_merge($values, $url->getParams());

		$this->baseString    = self::buildBasestring($this->requestMethod, $this->url, $this->params);

		// get the signature object
		$signature = self::getSignature($method);

		// generate the signature
		$values['oauth_signature'] = $signature->build($this->baseString, $consumerSecret, $tokenSecret);

		// build request
		$authorizationHeader = 'OAuth realm="psx", ' . self::buildAuthString($values);

		return $authorizationHeader;
	}

	/**
	 * Returns the signature object based on the $method throws an exception if
	 * the method is not supported
	 *
	 * @param string $method
	 * @return PSX\Oauth\SignatureAbstract
	 */
	public static function getSignature($method)
	{
		switch($method)
		{
			case 'HMAC-SHA1':
				return new Signature\HMACSHA1();
				break;

			case 'RSA-SHA1':
				return new Signature\RSASHA1();
				break;

			case 'PLAINTEXT':
				return new Signature\PLAINTEXT();
				break;

			default:
				throw new Exception('Invalid signature method');
				break;
		}
	}

	/**
	 * Build the string that we use in the authentication header
	 *
	 * @param array $data
	 * @return string
	 */
	public static function buildAuthString(array $data)
	{
		$str = array();

		foreach($data as $k => $v)
		{
			$str[] = self::urlEncode($k) . '="' . self::urlEncode($v) . '"';
		}

		return implode(', ', $str);
	}

	/**
	 * Builds the basestring for the signature.
	 *
	 * @see http://oauth.net/core/1.0a#rfc.section.9.1
	 * @param string $method
	 * @param PSX\Url $url
	 * @param array $data
	 * @return string
	 */
	public static function buildBasestring($method, Url $url, array $data)
	{
		$base = array();
		$base[] = self::urlEncode(self::getNormalizedMethod($method));
		$base[] = self::urlEncode(self::getNormalizedUrl($url));
		$base[] = self::urlEncode(self::getNormalizedParameters($data));

		return implode('&', $base);
	}

	/**
	 * Returns the method in uppercase
	 *
	 * @param string $method
	 * @return string
	 */
	public static function getNormalizedMethod($method)
	{
		return strtoupper($method);
	}

	/**
	 * Normalize the url like defined in
	 *
	 * @see http://oauth.net/core/1.0a#rfc.section.9.1.2
	 * @param PSX\Url $url
	 * @return false|string
	 */
	public static function getNormalizedUrl(Url $url)
	{
		$scheme = $url->getScheme();
		$host   = $url->getHost();
		$port   = $url->getPort();
		$path   = $url->getPath();

		// no port for 80 (http) and 443 (https)
		if((($port == 80 || empty($port)) && strcasecmp($scheme, 'http') == 0) || (($port == 443 || empty($port)) && strcasecmp($scheme, 'https') == 0))
		{
			$normalizedUrl = $scheme . '://' . $host . $path;
		}
		else
		{
			if(!empty($port))
			{
				$normalizedUrl = $scheme . '://' . $host . ':' . $port . $path;
			}
			else
			{
				throw new Exception('No port specified');
			}
		}

		return strtolower($normalizedUrl);
	}

	/**
	 * Returns the parameters that we need to create the basestring
	 *
	 * @param array $data
	 * @return string
	 */
	public static function getNormalizedParameters(array $data)
	{
		$params = array();

		$keys   = array_map('PSX\Oauth::urlEncode', array_keys($data));
		$values = array_map('PSX\Oauth::urlEncode', array_values($data));
		$data   = array_combine($keys, $values);


		uksort($data, 'strnatcmp');


		foreach($data as $k => $v)
		{
			if($k != 'oauth_signature')
			{
				$params[] = $k . '=' . $v;
			}
		}

		return implode('&', $params);
	}

	/**
	 * Encode values RFC3986
	 *
	 * @see http://oauth.net/core/1.0a#rfc.section.5.1
	 * @param string $data
	 * @return string
	 */
	public static function urlEncode($data)
	{
		return str_replace('%7E', '~', rawurlencode($data));
	}

	/**
	 * Decode values RFC3986
	 *
	 * @param string $data
	 * @return string
	 */
	public static function urlDecode($data)
	{
		return rawurldecode($data);
	}

	/**
	 * Returns the current timestamp used in a request
	 *
	 * @return integer
	 */
	public static function getTimestamp()
	{
		return time();
	}

	/**
	 * Returns the nonce used in a request
	 *
	 * @return string
	 */
	public static function getNonce()
	{
		return substr(md5(uniqid(mt_rand(), true)), 0, 16);
	}

	/**
	 * Returns the current version use in a request
	 *
	 * @return string
	 */
	public static function getVersion()
	{
		return '1.0';
	}

	/**
	 * This method returns an array with the support methods to sign a request
	 *
	 * @return array
	 */
	public static function getSupportedMethods()
	{
		return array('HMAC-SHA1', 'PLAINTEXT');
	}

	public function getRequestMethod()
	{
		return $this->requestMethod;
	}

	public function getUrl()
	{
		return $this->url;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function getBaseString()
	{
		return $this->baseString;
	}

	public function getHttpCode()
	{
		return $this->code;
	}

	public function getLastError()
	{
		return $this->lastError;
	}
}

