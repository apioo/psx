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

namespace PSX;

use PSX\OpenSsl\ErrorHandleTrait;
use PSX\OpenSsl\Exception as OpenSslException;
use PSX\OpenSsl\PKey;

/**
 * OpenSsl
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class OpenSsl
{
	use ErrorHandleTrait;

	public static function decrypt($data, $method, $password, $rawInput = false, $iv = '')
	{
		$return = openssl_decrypt($data, $method, $password, $rawInput, $iv);

		self::handleReturn($return);

		return $return;
	}

	public static function dhComputeKey($pubKey, PKey $dhKey)
	{
		$return = openssl_dh_compute_key($pubKey, $dhKey->getResource());

		self::handleReturn($return);

		return $return;
	}

	public static function digest($data, $func, $rawOutput = false)
	{
		$return = openssl_digest($data, $func, $rawOutput);

		self::handleReturn($return);

		return $return;
	}

	public static function encrypt($data, $method, $password, $rawOutput = false, $iv = '')
	{
		$return = openssl_encrypt($data, $method, $password, $rawOutput, $iv);

		self::handleReturn($return);

		return $return;
	}

	public static function errorString()
	{
		return openssl_error_string();
	}

	public static function getCipherMethods($aliases = false)
	{
		return openssl_get_cipher_methods($aliases);
	}

	public static function getMdMethods($aliases = false)
	{
		return openssl_get_md_methods($aliases);
	}

	public static function open($sealedData, &$openData, $envKey, PKey $key)
	{
		$return = openssl_open($sealedData, $openData, $envKey, $key->getResource());

		self::handleReturn($return);

		return $return;
	}

	public static function seal($data, &$sealedData, &$envKeys, array $pubKeys)
	{
		$pubKeyIds = array();
		foreach($pubKeys as $pubKey)
		{
			if($pubKey instanceof PKey)
			{
				$pubKeyIds[] = $pubKey->getPublicKey();
			}
			else
			{
				throw new OpenSslException('Pub keys must be an array containing PSX\OpenSsl\PKey instances');
			}
		}

		$return = openssl_seal($data, $sealedData, $envKeys, $pubKeyIds);

		self::handleReturn($return);

		return $return;
	}

	public static function sign($data, &$signature, PKey $key, $signatureAlg = OPENSSL_ALGO_SHA1)
	{
		$return = openssl_sign($data, $signature, $key->getResource(), $signatureAlg);

		self::handleReturn($return);

		return $return;
	}

	public static function verify($data, $signature, PKey $key, $signatureAlg = OPENSSL_ALGO_SHA1)
	{
		$return = openssl_verify($data, $signature, $key->getPublicKey(), $signatureAlg);

		self::handleReturn($return);

		return $return;
	}

	public static function privateDecrypt($data, &$decrypted, PKey $key, $padding = OPENSSL_PKCS1_PADDING)
	{
		$return = openssl_private_decrypt($data, $decrypted, $key->getResource(), $padding);

		self::handleReturn($return);

		return $return;
	}

	public static function privateEncrypt($data, &$crypted, PKey $key, $padding = OPENSSL_PKCS1_PADDING)
	{
		$return = openssl_private_encrypt($data, $crypted, $key->getResource(), $padding);

		self::handleReturn($return);

		return $return;
	}

	public static function publicDecrypt($data, &$decrypted, PKey $key, $padding = OPENSSL_PKCS1_PADDING)
	{
		$return = openssl_public_decrypt($data, $decrypted, $key->getPublicKey(), $padding);

		self::handleReturn($return);

		return $return;
	}

	public static function publicEncrypt($data, &$crypted, PKey $key, $padding = OPENSSL_PKCS1_PADDING)
	{
		$return = openssl_public_encrypt($data, $crypted, $key->getPublicKey(), $padding);

		self::handleReturn($return);

		return $return;
	}

	public static function randomPseudoBytes($length)
	{
		return openssl_random_pseudo_bytes($length);
	}
}
