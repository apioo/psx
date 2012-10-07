<?php
/*
 *  $Id: Oauth.php 663 2012-10-07 16:45:52Z k42b3.x@googlemail.com $
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

/**
 * This is a consumer implementation of OAuth Core 1.0. I would like to thank
 * Andy Smith for his reference implementation of OAuth many ideas of this
 * implementation are based on it.
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @author     Andy Smith <http://term.ie>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @see        http://tools.ietf.org/html/rfc5849
 * @category   PSX
 * @package    PSX_Oauth
 * @version    $Revision: 663 $
 */
class PSX_Oauth
{
	private $requestMethod;
	private $url;
	private $params;
	private $baseString;

	private $code;
	private $lastError;
	private $http;

	public function __construct(PSX_Http $http)
	{
		$this->http = $http;
	}

	/**
	 * Requests a new "request token" from the $url using the consumer key and
	 * secret. The $url must be valid request token endpoint. Returns an array
	 * with all key values pairs from the response i.e.
	 * <code>
	 *
	 * $response = $oauth->requestToken(...);
	 *
	 * $token       = $response->getToken();
	 * $tokenSecret = $response->getTokenSecret();
	 *
	 * </code>
	 *
	 * @see http://oauth.net/core/1.0a#rfc.section.6.1
	 * @param string $url
	 * @param string $consumerKey
	 * @param string $consumerSecret
	 * @param string $method
	 * @return PSX_Oauth_Provider_Data_Response
	 */
	public function requestToken(PSX_Url $url, $consumerKey, $consumerSecret, $method = 'HMAC-SHA1', $callback = false)
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
		$this->requestMethod = 'GET';
		$this->url           = $url;
		$this->params        = array_merge($values, $url->getParams());

		$this->baseString    = self::buildBasestring($this->requestMethod, $this->url, $this->params);


		// get the signature object
		$signature = self::getSignature($method);


		// generate the signature
		$values['oauth_signature'] = $signature->build($this->baseString, $consumerSecret);


		// request unauthorized token
		$request   = new PSX_Http_GetRequest($url, array(

			'Authorization' => 'OAuth realm="psx", ' . self::buildAuthString($values),
			'User-Agent'    => __CLASS__ . ' ' . PSX_Base::VERSION,

		));

		$response  = $this->http->request($request);
		$lastError = $this->http->getLastError();

		if(empty($lastError))
		{
			$this->code      = $response->getCode();
			$this->lastError = false;

			// parse the response
			$reader = new PSX_Data_Reader_Form();

			$dataResponse = new PSX_Oauth_Provider_Data_Response();

			$dataResponse->import($reader->read($response));

			return $dataResponse;
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
	 * @param string $url
	 * @param string $token
	 * @return void
	 */
	public function userAuthorization(PSX_Url $url, array $params = array())
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
	 *
	 * $response = $oauth->accessToken(...);
	 *
	 * $token       = $response->getToken();
	 * $tokenSecret = $response->getTokenSecret();
	 *
	 * </code>
	 *
	 * @see http://oauth.net/core/1.0a#rfc.section.6.3
	 * @param string $url
	 * @param string $consumerKey
	 * @param string $consumerSecret
	 * @param string $token
	 * @param string $tokenSecret
	 * @param string $verifier
	 * @param string $method
	 */
	public function accessToken(PSX_Url $url, $consumerKey, $consumerSecret, $token, $tokenSecret, $verifier, $method = 'HMAC-SHA1')
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
		$this->requestMethod = 'GET';
		$this->url           = $url;
		$this->params        = array_merge($values, $url->getParams());

		$this->baseString    = self::buildBasestring($this->requestMethod, $this->url, $this->params);


		// get the signature object
		$signature = self::getSignature($method);


		// generate the signature
		$values['oauth_signature'] = $signature->build($this->baseString, $consumerSecret, $tokenSecret);


		// request access token
		$request   = new PSX_Http_GetRequest($url, array(

			'Authorization' => 'OAuth realm="psx", ' . self::buildAuthString($values),
			'User-Agent'    => __CLASS__ . ' ' . PSX_Base::VERSION,

		));

		$response  = $this->http->request($request);
		$lastError = $this->http->getLastError();

		if(empty($lastError))
		{
			$this->code      = $response->getCode();
			$this->lastError = false;

			// parse the response
			$reader = new PSX_Data_Reader_Form();

			$dataResponse = new PSX_Oauth_Provider_Data_Response();

			$dataResponse->import($reader->read($response));

			return $dataResponse;
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
	 *
	 * <code>
	 * $header = array(
	 *
	 * 	'Authorization: ' . $oauth->getAuthorizationHeader(...),
	 *
	 * );
	 * </code>
	 *
	 * @param string $url
	 * @param string $consumerKey
	 * @param string $consumerSecret
	 * @param string $token
	 * @param string $tokenSecret
	 * @param string $method
	 * @param string $requestMethod
	 * @return string
	 */
	public function getAuthorizationHeader(PSX_Url $url, $consumerKey, $consumerSecret, $token, $tokenSecret, $method = 'HMAC-SHA1', $requestMethod = 'GET')
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
	 * @return oauth_isignature
	 */
	public static function getSignature($method)
	{
		switch($method)
		{
			case 'HMAC-SHA1':

				return new PSX_Oauth_Signature_HMACSHA1();

				break;

			case 'RSA-SHA1':

				return new PSX_Oauth_Signature_RSASHA1();

				break;

			case 'PLAINTEXT':

				return new PSX_Oauth_Signature_PLAINTEXT();

				break;

			default:

				throw new PSX_Oauth_Exception('Invalid signature method');

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
	 * @param string $url
	 * @param array $data
	 * @return string
	 */
	public static function buildBasestring($method, PSX_Url $url, array $data)
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
	 * @param string $url
	 * @return false|string
	 */
	public static function getNormalizedUrl(PSX_Url $url)
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
				throw new PSX_Oauth_Exception('No port specified');
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

		$keys   = array_map('PSX_Oauth::urlEncode', array_keys($data));
		$values = array_map('PSX_Oauth::urlEncode', array_values($data));
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
	 * The response has an format like "name=value&name=value" we use the
	 * parse_str function to create an array with the appropriated key value
	 * pairs. You should note that parse_str uses the url_decode function on the
	 * data.
	 *
	 * @param string $response
	 * @return PSX_Oauth_Provider_Data_Response
	 */
	public static function parseResponse($response)
	{
		$data = array();

		parse_str($response, $data);


		$token       = null;
		$tokenSecret = null;

		if(isset($data['oauth_token']))
		{
			$token = $data['oauth_token'];
		}

		if(isset($data['oauth_token_secret']))
		{
			$tokenSecret = $data['oauth_token_secret'];
		}


		$response = new PSX_Oauth_Provider_Data_Response($token, $tokenSecret);

		return $response;
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

