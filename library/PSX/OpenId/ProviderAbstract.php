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

namespace PSX\OpenId;

use PSX\Exception;
use PSX\Data\ReaderInterface;
use PSX\Controller\ApiAbstract;
use PSX\OpenId;
use PSX\OpenId\Provider\Association;
use PSX\OpenId\Provider\Data\AssociationImporter;
use PSX\OpenId\Provider\Data\AssociationRequest;
use PSX\OpenId\Provider\Data\ResImporter;
use PSX\OpenId\Provider\Data\ResRequest;
use PSX\OpenId\Provider\Data\SetupImporter;
use PSX\OpenId\Provider\Data\SetupRequest;
use PSX\OpenSsl;
use PSX\OpenSsl\PKey;
use PSX\Url;

/**
 * ProviderAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ProviderAbstract extends ApiAbstract
{
	const NS   = 'http://specs.openid.net/auth/2.0';
	const DH_P = 'dcf93a0b883972ec0e19989ac5a2ce310e1d37717e8d9571bb7623731866e61ef75a2e27898b057f9891c2e27a639c3f29b60814581cd3b2ca3986d2683705577d45c2e7e52dc81c7a171876e5cea74b1448bfdfaf18828efd2519f14e45e3826634af1949e5b535cc829a483b8a76223e5d490a257f05bdff16f2fb22c583ab';
	const DH_G = '02';

	protected function handle()
	{
		$ns   = isset($_REQUEST['openid_ns'])   ? $_REQUEST['openid_ns']   : null;
		$mode = isset($_REQUEST['openid_mode']) ? $_REQUEST['openid_mode'] : null;

		if($ns != self::NS)
		{
			throw new Exception('Namespace not set or invalid');
		}

		switch($mode)
		{
			case 'associate':

				try
				{
					$request  = new AssociationRequest();
					$importer = new AssociationImporter();
					$importer->import($request, $this->getRequest(ReaderInterface::GPC));

					$expiresIn = (integer) $this->onAsocciation($request->getAssociation());

					if($expiresIn <= 0)
					{
						$expiresIn = 46800; // fallback
					}

					echo OpenId::keyValueEncode(array_merge(array(
						'ns'         => self::NS,
						'expires_in' => $expiresIn,
					), $request->getFields()));
				}
				catch(\Exception $e)
				{
					echo OpenId::keyValueEncode(array(
						'ns'         => self::NS,
						'error'      => $e->getMessage(),
						'error_code' => 'unsupported-type',
					));
				}

				break;

			case 'checkid_immediate':
			case 'checkid_setup':

				try
				{
					$request  = new SetupRequest();
					$importer = new SetupImporter();
					$importer->import($request, $this->getRequest(ReaderInterface::GPC));

					$request->setImmediate($mode == 'checkid_immediate');

					$this->onCheckidSetup($request);
				}
				catch(\Exception $e)
				{
					$returnTo = $request->getReturnTo();

					if($returnTo instanceof Url)
					{
						if($request->isImmediate())
						{
							$mode = 'setup_needed';
						}
						else
						{
							$mode = 'cancel';
						}

						$returnTo->addParam('openid.ns', self::NS);
						$returnTo->addParam('openid.mode', $mode);
						//$returnTo->addParam('openid.error', $e->getMessage());

						header('Location: ' . strval($returnTo));
						exit;
					}
					else
					{
						throw $e;
					}
				}

				break;

			case 'check_authentication':

				try
				{
					$request  = new ResRequest();
					$importer = new ResImporter();
					$importer->import($request, $this->getRequest(ReaderInterface::GPC));

					if($this->onCheckAuthentication($request) === true)
					{
						echo OpenId::keyValueEncode(array(
							'ns'       => self::NS,
							'is_valid' => 'true',
						));
					}
					else
					{
						throw new Exception('Authentication not successful');
					}
				}
				catch(\Exception $e)
				{
					echo OpenId::keyValueEncode(array(

						'ns'       => self::NS,
						'is_valid' => 'false',

					));
				}

				break;

			default:

				throw new Exception('Invalid mode');

				break;
		}

		exit;
	}

	/**
	 * onAsocciation
	 *
	 * Is called if a user performs an asocciation request. Should return the
	 * lifetime in seconds of this association
	 *
	 * @return void
	 */
	abstract public function onAsocciation(Association $assoc);

	/**
	 * onCheckidSetup
	 *
	 * Is called if a user peforms an openid checkid_setup request
	 *
	 * @return void
	 */
	abstract public function onCheckidSetup(SetupRequest $request);

	/**
	 * getAuthentication
	 *
	 * Returns the authentication response wich was previously send to the user
	 * by the assoc_handle.
	 *
	 * @return PSX\OpenId\Data\Authentication
	 */
	abstract public function onCheckAuthentication(ResRequest $request);

	/**
	 * getExtension
	 *
	 * Parses the $params array for extensions with the namespace $ns. The
	 * $params should be the values of $_GET or $_POST where the dot is replaced
	 * with an underscore
	 *
	 * @return array
	 */
	public static function getExtension(array $params, $ns)
	{
		$values = array();
		$alias  = null;

		// find alias
		foreach($params as $k => $v)
		{
			if($v == $ns)
			{
				$alias = substr($k, 10);
			}
		}

		if(!empty($alias))
		{
			// get values
			$len = strlen($alias);

			foreach($params as $k => $v)
			{
				if(substr($k, 0, 7 + $len) == 'openid_' . $alias)
				{
					$key = substr($k, 8 + $len);

					if(!empty($key))
					{
						$values[$key] = $v;
					}
				}
			}
		}

		return $values;
	}

	public static function generateHandle()
	{
		return sha1(uniqid() . time() . rand(0, 1024));
	}

	public static function generateDh($dhGen, $dhModulus, $dhConsumerPublic, $dhFunc, $secret)
	{
		if(empty($dhConsumerPublic))
		{
			throw new Exception('Empty "openid.dh_consumer_public"');
		}

		$g = empty($dhGen)     ? pack('H*', self::DH_G) : base64_decode($dhGen);
		$p = empty($dhModulus) ? pack('H*', self::DH_P) : base64_decode($dhModulus);

		$dhKey   = self::createDhKey($p, $g);
		$details = $dhKey->getDetails();
		$dh      = isset($details['dh']) ? $details['dh'] : null;

		if(empty($dh))
		{
			throw new Exception('Could not get dh details');
		}

		$sec    = OpenSsl::dhComputeKey(base64_decode($dhConsumerPublic), $dhKey);
		$digest = OpenSsl::digest(self::btwoc($sec), $dhFunc, true);

		$res = array(

			'pubKey' => base64_encode(self::btwoc($dh['pub_key'])),
			'macKey' => base64_encode($digest ^ $secret),

		);

		return $res;
	}

	public static function createDhKey($p, $g, $privKey = null)
	{
		$dhOptions = array('p' => $p, 'g' => $g);

		if($privKey !== null)
		{
			$dhOptions['priv_key'] = $privKey;
		}

		return new PKey(array('dh' => $dhOptions));
	}

	public static function btwoc($str)
	{
		if(ord($str[0]) > 127)
		{
			return "\0" . $str;
		}

		return $str;
	}

	public static function randomBytes($len)
	{
		$key = '';

		for($i = 0; $i < $len; $i++)
		{
			$key.= chr(mt_rand(0, 255));
		}

		return $key;
	}
}

