<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Json;

use PSX\Exception;
use PSX\Json;

/**
 * WebSignature
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
