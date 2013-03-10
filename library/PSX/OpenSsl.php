<?php
/*
 *  $Id: OpenSsl.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

namespace PSX;

use PSX\OpenSsl\PKey;

/**
 * PSX_OpenSsl
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_OpenSsl
 * @version    $Revision: 480 $
 */
class OpenSsl
{
	public static function decrypt($data, $method, $password, $rawInput = false, $iv = '')
	{
		$ret = openssl_decrypt($data, $method, $password, $rawInput, $iv);

		if($ret === false)
		{
			throw new Exception('Could not decrypt data');
		}

		return $ret;
	}

	public static function dhComputeKey($pubKey, PKey $dhKey)
	{
		$ret = openssl_dh_compute_key($pubKey, $dhKey->getResource());

		if($ret === false)
		{
			throw new Exception('Could not compute secret');
		}

		return $ret;
	}

	public static function digest($data, $func, $rawOutput = false)
	{
		$ret = openssl_digest($data, $func, $rawOutput);

		if($ret === false)
		{
			throw new Exception('Could not compute digest');
		}

		return $ret;
	}

	public static function encrypt($data, $method, $password, $rawOutput = false, $iv = '')
	{
		$ret = openssl_encrypt($data, $method, $password, $rawOutput, $iv);

		if($ret === false)
		{
			throw new Exception('Could not encrypt data');
		}

		return $ret;
	}

	public static function errorString()
	{
		return openssl_error_string();
	}

	public static function freeKey($keyIdentifier)
	{
		openssl_free_key($keyIdentifier);
	}

	public static function getCipherMethods($aliases = false)
	{
		return openssl_get_cipher_methods($aliases);
	}

	public static function getMdMethods($aliases = false)
	{
		return openssl_get_md_methods($aliases);
	}

	public static function getPrivateKey($key, $passphrase = null)
	{
		return PSX_OpenSsl_PKey::getPrivate($key, $passphrase);
	}

	public static function getPublicKey($certificate)
	{
		return PSX_OpenSsl_PKey::getPublic($certificate);
	}

	public static function open($sealedData, &$openData, $envKey, $privKeyId)
	{
		return openssl_open($sealedData, $openData, $envKey, $privKeyId);
	}

	public static function privateDecrypt($data, &$decrypted, $key, $padding = null)
	{
		return openssl_private_decrypt($data, $decrypted, $key, $padding);
	}

	public static function privateEncrypt($data, &$crypted, $key, $padding = null)
	{
		return openssl_private_encrypt($data, $crypted, $key, $padding);
	}

	public static function publicDecrypt($data, &$decrypted, $key, $padding = null)
	{
		return openssl_public_decrypt($data, $decrypted, $key, $padding);
	}

	public static function publicEncrypt($data, &$crypted, $key, $padding = null)
	{
		return openssl_public_encrypt($data, $crypted, $key, $padding);
	}

	public static function randomPseudoBytes($length, &$cryptoStrong)
	{
		return openssl_random_pseudo_bytes($length, $cryptoStrong);
	}

	public static function seal($data, &$sealedData, &$envKeys, $pubKeyIds)
	{
		return openssl_seal($data, $sealedData, $envKeys, $pubKeyIds);
	}

	public static function sign($data, &$signature, $privKeyId, $signatureAlg = null)
	{
		return openssl_sign($data, $signature, $privKeyId, $signatureAlg);
	}

	public static function verify($data, $signature, $pubKeyId, $signatureAlg = null)
	{
		return openssl_verify($data, $signature, $pubKeyId, $signatureAlg);
	}
}