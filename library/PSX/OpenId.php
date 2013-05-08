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

use PSX\Html\Parse;
use PSX\Html\Parse\Element;
use PSX\Http\GetRequest;
use PSX\Http\PostRequest;
use PSX\OpenId\ExtensionInterface;
use PSX\OpenId\Identity;
use PSX\OpenId\ProviderAbstract;
use PSX\OpenId\StoreInterface;
use PSX\OpenId\Provider\Association;
use PSX\OpenSsl\PKey;

/**
 * OpenId
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class OpenId
{
	public static $xriGlobalContextSymbol = array('=', '@', '+', '$', '!', '(');
	public static $supportedAssocTypes    = array('HMAC-SHA1', 'HMAC-SHA256');
	public static $supportedSessionTypes  = array('no-encryption', 'DH-SHA1', 'DH-SHA256');

	private $types  = array();
	private $data   = array();
	private $params = array();

	private $identity;
	private $returnTo;
	private $claimedId;

	private $http;
	private $trustRoot;
	private $store;
	private $assoc;

	public function __construct(Http $http, $trustRoot, StoreInterface $store = null)
	{
		$this->http      = $http;
		$this->trustRoot = $trustRoot;
		$this->store     = $store;
	}

	public function initialize($identity, $returnTo)
	{
		$this->claimedId = $identity;
		$this->identity  = $this->discovery($identity);
		$this->returnTo  = $returnTo;

		$this->checkAssociation();
	}

	/**
	 * Piggybacks extension parameters for an openid request. If we have
	 * discovered an XRDS document we check whether the service support the
	 * extension if we have no informations what types are supported by the
	 * endpoint we add the params and hope that the server support it
	 *
	 * @param PSX\OpenId\ExtensionInterface $extension
	 * @return void
	 */
	public function add(ExtensionInterface $extension)
	{
		if(empty($this->types))
		{
			$this->params+= $extension->getParams();
		}
		else
		{
			if(in_array($extension->getNs(), $this->types))
			{
				$this->params+= $extension->getParams();
			}
			else
			{
				throw new Exception('The extension ' . $extension->getNs() . ' is not supported by the service');
			}
		}
	}

	public function redirect(array $overrideParams = array())
	{
		$params = array(

			'openid.ns'        => ProviderAbstract::NS,
			'openid.mode'      => 'checkid_setup',
			'openid.return_to' => $this->returnTo,
			'openid.realm'     => $this->trustRoot,

		) + $this->params;

		// add identity and claimend id
		$identity = (string) $this->identity->getLocalId();

		if(!empty($identity))
		{
			$params['openid.claimed_id'] = $this->claimedId;
			$params['openid.identity']   = $identity;
		}
		else
		{
			$params['openid.claimed_id'] = $this->claimedId;
			$params['openid.identity']   = $this->claimedId;
		}

		// add association if established
		if($this->assoc !== null)
		{
			$params['openid.assoc_handle'] = $this->assoc->getAssocHandle();
		}

		// override params
		if(!empty($overrideParams))
		{
			foreach($overrideParams as $k => $v)
			{
				$params[$k] = $v;
			}
		}

		// add params and redirect
		$url = $this->identity->getServer();
		$url->addParams($params);

		header('Location: ' . strval($url));
		exit;
	}

	/**
	 * Verifies whether the authentication was successful or not. Must be called
	 * from the callback where the op redirects the user back. The method
	 * throws an exception on error or returns true if the authentication was
	 * verified
	 *
	 * @return boolean
	 */
	public function verify()
	{
		$data = $_GET;
		$mode = isset($data['openid_mode']) ? $data['openid_mode'] : false;

		if($mode == 'cancel')
		{
			throw new Exception('User has canceled the request');
		}

		if($mode == 'error')
		{
			$msg = isset($data['openid_error']) ? $data['openid_error'] : false;

			throw new Exception(!empty($msg) ? $msg : 'An error occured but no message was specified');
		}

		if($mode == 'id_res')
		{
			// check values
			$diff = array_diff(array('openid_ns', 'openid_mode', 'openid_op_endpoint', 'openid_return_to', 'openid_response_nonce', 'openid_assoc_handle', 'openid_signed', 'openid_sig'), array_keys($data));

			if(count($diff) > 0)
			{
				throw new Exception('Missing fields ' . implode(', ', $diff));
			}

			if($data['openid_ns'] != ProviderAbstract::NS)
			{
				throw new Exception('Invalid namesspace');
			}

			$opEndpoint    = new Url($data['openid_op_endpoint']);
			$returnTo      = new Url($data['openid_return_to']);
			$responseNonce = $data['openid_response_nonce'];
			$assocHandle   = $data['openid_assoc_handle'];
			$signed        = explode(',', $data['openid_signed']);
			$sig           = $data['openid_sig'];

			$claimedId     = isset($data['openid_claimed_id']) ? $data['openid_claimed_id'] : null;
			$identity      = isset($data['openid_identity'])   ? $data['openid_identity']   : null;

			// try to load association from store if available
			$assoc = null;

			if($this->store !== null)
			{
				$assoc = $this->store->load($data['openid_op_endpoint'], $assocHandle);
			}

			// check authentication
			$isAuthenticated = false;

			if($assoc === null)
			{
				$isAuthenticated = $this->checkAuthentication($opEndpoint, $data);
			}
			else
			{
				$signature       = self::buildSignature(self::extractParams($data), $signed, $assoc->getSecret(), $assoc->getAssocType());
				$isAuthenticated = strcmp($signature, $sig) === 0;
			}

			// if authentication was successful
			if($isAuthenticated === true)
			{
				// import the signed parameters
				if(!empty($signed))
				{
					foreach($signed as $sign)
					{
						$k = 'openid_' . str_replace('.', '_', $sign);
						$v = isset($data[$k]) ? $data[$k] : null;

						if($v !== null)
						{
							$this->data[$k] = $v;
						}
					}
				}

				// set identity
				$server    = isset($this->data['openid_op_endpoint']) ? $this->data['openid_op_endpoint'] : null;
				$localId   = isset($this->data['openid_identity'])    ? $this->data['openid_identity']    : null;
				$claimedId = isset($this->data['openid_claimed_id'])  ? $this->data['openid_claimed_id']  : null;

				if(!empty($server))
				{
					$this->identity  = new Identity($server, $localId);
					$this->claimedId = $claimedId;
				}
				else
				{
					throw new Exception('Missing data in response');
				}

				return true;
			}
			else
			{
				throw new Exception('Authentication fails');
			}
		}
	}

	public function hasExtension($ns)
	{
		return in_array($ns, $this->types);
	}

	/**
	 * Returns the data that we have received via the callback
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Returns an array with all service types that the discovered service
	 * support (namespaces)
	 *
	 * @return array
	 */
	public function getTypes()
	{
		return $this->types;
	}

	public function getClaimedId()
	{
		return $this->claimedId;
	}

	public function getIdentity()
	{
		return $this->identity;
	}

	public function getReturnTo()
	{
		return $this->returnTo;
	}

	/**
	 * Returns an identifier of the user wich can be used to identify this
	 * particular user.
	 *
	 * @return string
	 */
	public function getIdentifier()
	{
		if($this->identity instanceof Identity)
		{
			$identifier = $this->identity->getLocalId() === null ? $this->getClaimedId() : $this->identity->getLocalId();

			return self::normalizeIdentifier($identifier);
		}
		else
		{
			throw new Exception('Identity not discovered');
		}
	}

	/**
	 * Discovers an openid identity (URI or XRI). It uses the xri.net web
	 * service to resolve XRIs. Returns either an PSX\OpenId\Identity
	 *
	 * @param string $identity
	 * @return PSX\OpenId\Identity
	 */
	private function discovery($identity)
	{
		$identity = $this->normalizeUri($identity);

		// we could get the identity via XRI
		if($identity instanceof Identity)
		{
			return $identity;
		}
		// its an url wich we must discover
		else if($identity instanceof Url)
		{
			# YADIS discovery
			$discoveredIdentity = $this->fetchXrds($identity);

			# HTML based discovery
			if($discoveredIdentity === false)
			{
				$request  = new GetRequest($identity, array(
					'User-Agent' => __CLASS__ . ' ' . Base::VERSION
				));
				$request->setFollowLocation(true);

				$response = $this->http->request($request);

				return $this->htmlBasedDiscovery($response->getBody());
			}
			else
			{
				return $discoveredIdentity;
			}
		}
		else
		{
			throw new Exception('Invalid openid identity');
		}
	}

	/**
	 * We make a YADIS dicovery on the url. If we have found an fitting
	 * service the method returns the endpoint uri and if available a
	 * claimed id else it returns false
	 *
	 * @param PSX\Url $url
	 * @return array
	 */
	private function fetchXrds(Url $url)
	{
		$yadis = new Yadis($this->http);
		$xrds  = $yadis->discover($url);

		if($xrds !== false && isset($xrds->service))
		{
			// OP Identifier Element
			foreach($xrds->service as $service)
			{
				if(in_array('http://specs.openid.net/auth/2.0/server', $service->getType()))
				{
					$this->types = $service->getType();

					$identity = new Identity($service->getUri());

					return $identity;
				}
			}

			// Claimed Identifier Element
			foreach($xrds->service as $service)
			{
				if(in_array('http://specs.openid.net/auth/2.0/signon', $service->getType()))
				{
					$this->types = $service->getType();

					$identity = new Identity($service->getUri(), $service->getLocalId());

					return $identity;
				}
			}
		}

		return false;
	}

	/**
	 * Resolves the $uri either to an PSX\OpenId\Identity if its an XRI or
	 * to an PSX\Url
	 *
	 * @return PSX\OpenId\Identity|PSX\Url
	 */
	public function normalizeUri($uri)
	{
		if(!empty($uri))
		{
			$uri   = strtolower($uri);
			$isXri = false;

			if(substr($uri, 0, 6) == 'xri://')
			{
				$uri   = substr($uri, 6);
				$isXri = true;
			}

			if(in_array($uri[0], self::$xriGlobalContextSymbol))
			{
				$isXri = true;
			}

			if($isXri !== true)
			{
				if(substr($uri, 0, 7) != 'http://' && substr($uri, 0, 8) != 'https://')
				{
					$uri = 'http://' . $uri;
				}

				$url = new Url($uri);

				if($url->getScheme() == 'http' || $url->getScheme() == 'https')
				{
					return $url;
				}
				else
				{
					throw new Exception('Unknown protocol in identity');
				}
			}
			else
			{
				return $this->discoverXriIdentity($uri);
			}
		}

		return false;
	}

	public function discoverXriIdentity($identity)
	{
		// we use the XRI resolver at xri.net
		$request  = new GetRequest(new Url('http://xri.net/' . $identity), array(
			'User-Agent' => __CLASS__ . ' ' . Base::VERSION
		));
		$response = $this->http->request($request);

		// we accept all 3xx redirect status codes if we dont
		// get redirected we couldnt resolve the XRI
		$header = $response->getHeader();

		if($response->getCode() >= 300 && $response->getCode() < 400 && isset($header['location']))
		{
			// we make an YADIS request to the resolved URI to get
			// an XRDS document
			$location = new Url($header['location']);

			return $this->fetchXrds($location);
		}
		else
		{
			throw new Exception('Couldnt resolve XRI');
		}
	}

	private function htmlBasedDiscovery($html)
	{
		$parse   = new Parse($html);
		$element = new Element('link', array('rel' => 'openid2.provider'));
		$server  = $parse->fetchAttrFromHead($element, 'href');

		if(empty($server))
		{
			$element->setAttributes(array('rel' => 'openid.server'));

			$server = $parse->fetchAttrFromHead($element, 'href');
		}


		$element = new Element('link', array('rel' => 'openid2.local_id'));
		$localId = $parse->fetchAttrFromHead($element, 'href');

		if(empty($localId))
		{
			$element->setAttributes(array('rel' => 'openid.delegate'));

			$localId = $parse->fetchAttrFromHead($element, 'href');
		}


		if(!empty($server))
		{
			$identity = new Identity($server, $localId);

			return $identity;
		}
		else
		{
			throw new Exception('Couldnt find server in identifier');
		}
	}

	/**
	 * Checks whether the store contains an association for this server if not
	 * we try to establish an association. If no store is set we are in dump
	 * mode
	 *
	 * @return void
	 */
	public function checkAssociation()
	{
		if($this->store !== null)
		{
			$assoc = $this->store->load($this->identity->getServer());

			if($assoc === null)
			{
				$assoc = $this->establishAssociaton();

				$this->store->save($this->identity->getServer(), $assoc);
			}

			$this->assoc = $assoc;
		}
	}

	/**
	 * Tries to establish a association with the op if a store is available. The
	 * method returns null or PSX\OpenId\Provider\Data\Association. Discovery
	 * must be made before calling this method
	 *
	 * @return PSX\OpenId\Provider\Data\Association|null
	 */
	private function establishAssociaton($assocType = 'HMAC-SHA256', $sessionType = 'DH-SHA256')
	{
		// request association
		$g = pack('H*', ProviderAbstract::DH_G);
		$p = pack('H*', ProviderAbstract::DH_P);

		$pkey    = new PKey(array('dh' => array('p' => $p, 'g' => $g)));
		$details = $pkey->getDetails();
		$params  = array(

			'openid.ns'                 => ProviderAbstract::NS,
			'openid.mode'               => 'associate',
			'openid.assoc_type'         => $assocType,
			'openid.session_type'       => $sessionType,
			'openid.dh_modulus'         => base64_encode(ProviderAbstract::btwoc($details['dh']['p'])),
			'openid.dh_gen'             => base64_encode(ProviderAbstract::btwoc($details['dh']['g'])),
			'openid.dh_consumer_public' => base64_encode(ProviderAbstract::btwoc($details['dh']['pub_key'])),

		);

		$request  = new PostRequest($this->identity->getServer(), array(
			'User-Agent' => __CLASS__ . ' ' . Base::VERSION
		), $params);
		$response = $this->http->request($request);

		if($response->getCode() == 200)
		{
			$data = self::keyValueDecode($response->getBody());

			// check values
			$diff = array_diff(array('ns', 'assoc_handle', 'session_type', 'assoc_type', 'expires_in'), array_keys($data));

			if(count($diff) > 0)
			{
				throw new Exception('Missing fields ' . implode(', ', $diff));
			}

			if($data['ns'] != ProviderAbstract::NS)
			{
				throw new Exception('Invalid namesspace');
			}

			if(!in_array($data['session_type'], self::$supportedSessionTypes))
			{
				throw new Exception('Invalid session type');
			}

			if(!in_array($data['assoc_type'], self::$supportedAssocTypes))
			{
				throw new Exception('Invalid assoc type');
			}

			// decrypt shared secret
			if($data['session_type'] != 'no-encryption')
			{
				if(!isset($data['dh_server_public']))
				{
					throw new Exception('DH server public not set');
				}

				if(!isset($data['enc_mac_key']))
				{
					throw new Exception('Encoded mac key not set');
				}

				$dhFunc       = str_replace('DH-', '', $data['session_type']);
				$serverPub    = base64_decode($data['dh_server_public']);
				$dhSec        = OpenSsl::dhComputeKey($serverPub, $pkey);
				$sec          = OpenSsl::digest(ProviderAbstract::btwoc($dhSec), $dhFunc, true);
				$serverSecret = base64_encode($sec ^ base64_decode($data['enc_mac_key']));
			}
			else
			{
				if(!isset($data['mac_key']))
				{
					throw new Exception('Mac key not set');
				}

				$dhFunc       = null;
				$serverSecret = $data['mac_key'];
			}

			// build association
			$assoc = new Association();
			$assoc->setAssocHandle($data['assoc_handle']);
			$assoc->setAssocType($data['assoc_type']);
			$assoc->setSessionType($data['session_type']);
			$assoc->setSecret($serverSecret);
			$assoc->setExpire($data['expires_in']);

			return $assoc;
		}
		else
		{
			throw new Exception('Could not establish associaton received ' . $response->getCode());
		}
	}

	/**
	 * If we could not establish an association we must make an http request
	 * to check whether the identity is valid
	 *
	 * @return boolean
	 */
	public function checkAuthentication($opEndpoint, array $data)
	{
		// verify the identity
		$params = array(

			'openid.mode'   => 'check_authentication',
			'openid.signed' => $data['openid_signed'],
			'openid.sig'    => $data['openid_sig'],

		);

		// you should note that php replaces . (dot) in the query fragment with
		// _ (underscore) i.e. index.php?openid.foo=bar can you access with
		// $_GET['openid_foo']
		$signed = isset($data['openid_signed']) ? explode(',', $data['openid_signed']) : null;

		if(!empty($signed))
		{
			foreach($signed as $sign)
			{
				$k = 'openid_' . str_replace('.', '_', $sign);
				$v = isset($data[$k]) ? $data[$k] : null;

				if($v !== null && $k != 'openid_mode')
				{
					$params['openid.' . $sign] = $v;
				}
			}
		}
		else
		{
			throw new Exception('No values are signed');
		}

		// make request
		$request  = new PostRequest($opEndpoint, array(
			'User-Agent' => __CLASS__ . ' ' . Base::VERSION
		), $params);
		$response = $this->http->request($request);

		if($response->getCode() == 200)
		{
			$data = self::keyValueDecode($response->getBody());

			if(isset($data['error']))
			{
				throw new Exception($data['error']);
			}

			if(isset($data['is_valid']) && $data['is_valid'] == 'true')
			{
				return true;
			}
			else
			{
				throw new Exception('Identity is not valid');
			}
		}
		else
		{
			throw new Exception('Invalid response code ' . $response->getCode());
		}
	}

	public static function keyValueEncode(array $data)
	{
		$content = '';

		foreach($data as $k => $v)
		{
			$content.= $k . ':' . $v . "\n";
		}

		return $content;
	}

	public static function keyValueDecode($data)
	{
		$params = array();
		$lines  = explode("\n", $data);

		foreach($lines as $line)
		{
			$line = trim($line);

			if(!empty($line))
			{
				$pair = explode(':', $line, 2);

				if(isset($pair[0]) && isset($pair[1]))
				{
					$k = str_replace(array("\n", ':'), '', trim($pair[0]));
					$v = str_replace(array("\n"),      '', trim($pair[1]));

					$params[$k] = $v;
				}
			}
		}

		return $params;
	}

	public static function normalizeIdentifier($identifier)
	{
		$normalize  = '';
		$identifier = trim(trim(strtolower($identifier)), '/');

		$url = parse_url($identifier);

		if(isset($url['user']))
		{
			$normalize.= $url['user'] . '@';
		}

		if(isset($url['host']))
		{
			$normalize.= $url['host'];
		}
		else
		{
			return false;
		}

		if(isset($url['path']))
		{
			$normalize.= $url['path'];
		}

		return $normalize;
	}

	/**
	 * Builds the signaure. The array params contains all openid.* values
	 * without the openid. prefix. $signed is an array containing all fields
	 * wich are signed. $secret is the base64_encoded share secret.
	 *
	 * @param array $params
	 * @param array $signed
	 * @param string $secret
	 * @param string $hashAlgo
	 */
	public static function buildSignature(array $params, array $signed, $secret, $hashAlgo = 'HMAC-SHA256')
	{
		// check values
		$mustSigned = array('op_endpoint', 'return_to', 'response_nonce', 'assoc_handle');

		if(isset($params['claimed_id']) && isset($params['identity']))
		{
			$mustSigned[] = $params['claimed_id'];
			$mustSigned[] = $params['identity'];
		}

		if(count(array_diff(array_intersect($mustSigned, $signed), $mustSigned)) > 0)
		{
			throw new Exception('You must sign at least: ' . implode(',', $mustSigned));
		}

		if(in_array($hashAlgo, self::$supportedAssocTypes))
		{
			$hashAlgo = str_replace('HMAC-', '', $hashAlgo);
		}
		else
		{
			throw new Exception('Invalid hash algo');
		}

		// build base string
		$data = array();

		foreach($signed as $sign)
		{
			$k1 = $sign;
			$k2 = str_replace('.', '_', $sign);

			$v = isset($params[$k1]) ? $params[$k1] : (isset($params[$k2]) ? $params[$k2] : null);

			if($v !== null)
			{
				$data[$sign] = $v;
			}
		}

		$baseString = self::keyValueEncode($data);
		$signature  = base64_encode(hash_hmac($hashAlgo, $baseString, base64_decode($secret), true));

		return $signature;
	}

	/**
	 * Returns an subset of the $array where the key contains the prefix
	 * "openid." or "openid_"
	 *
	 * @param $params
	 * @return array
	 */
	public static function extractParams(array $params)
	{
		$data = array();

		foreach($params as $k => $v)
		{
			$prefix = substr($k, 0, 7);

			if($prefix == 'openid.' || $prefix == 'openid_')
			{
				$data[substr($k, 7)] = $v;
			}
		}

		return $data;
	}
}

