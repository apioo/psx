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

namespace PSX\Json;

use PSX\Exception;
use PSX\Json;

/**
 * WebSignature
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://tools.ietf.org/html/draft-ietf-jose-json-web-signature-39
 */
class WebSignature extends WebToken
{
	const ALGORITHM          = 'alg';
	const JWK_SET_URL        = 'jku';
	const JSON_WEB_KEY       = 'jwk';
	const KEY_ID             = 'kid';
	const X_509_URL          = 'x5u';
	const X_509_CERT_CHAIN   = 'x5c';
	const X_509_CERT_SHA_1   = 'x5t';
	const X_509_CERT_SHA_256 = 'x5t#S256';
	const CRITICAL           = 'crit';

	protected $algos = array(
		'HS256' => 'sha256',
		'HS384' => 'sha384',
		'HS512' => 'sha512',
	);

	/**
	 * Validates the given signature using the $key. Returns true if the 
	 * signature is valid
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function validate($key)
	{
		if(empty($this->signature))
		{
			throw new Exception('No foreign signature available');
		}

		return strcmp($this->signature, $this->getEncodedSignature($key)) === 0;
	}

	/**
	 * Returns this signature in the JWS Compact Serialization format
	 *
	 * @see http://tools.ietf.org/html/draft-ietf-jose-json-web-signature-39#section-3.1
	 * @param string $key
	 * @return string
	 */
	public function getCompact($key)
	{
		$headers   = self::base64Encode(Json::encode($this->headers));
		$claim     = self::base64Encode($this->claim);
		$signature = $this->getEncodedSignature($key);

		return $headers . '.' . $claim . '.' . $signature;
	}

	/**
	 * Returns this signature in the JWS JSON Serialization format
	 *
	 * @see http://tools.ietf.org/html/draft-ietf-jose-json-web-signature-39#section-3.2
	 * @param string $key
	 * @return string
	 */
	public function getJson($key)
	{
		$signature = array();
		$signature['protected'] = self::base64Encode(Json::encode($this->headers));
		$signature['payload']   = self::base64Encode($this->claim);
		$signature['signature'] = $this->getEncodedSignature($key);

		return Json::encode($signature);
	}

	protected function getEncodedSignature($key)
	{
		$alg = strtoupper($this->getHeader(self::ALGORITHM));

		if(isset($this->algos[$alg]))
		{
			$header    = self::base64Encode(json_encode($this->headers));
			$claim     = self::base64Encode($this->claim);

			$data      = $header . '.' . $claim;
			$signature = self::base64Encode(hash_hmac($this->algos[$alg], $data, $key, true));

			return $signature;
		}
		else
		{
			throw new Exception('Unsupported signature algorithm');
		}
	}
}
