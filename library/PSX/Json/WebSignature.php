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

namespace PSX\Json;

use PSX\Json;

/**
 * WebSignature
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://tools.ietf.org/html/draft-ietf-jose-json-web-signature-11
 */
class WebSignature extends WebToken
{
	protected $reservedHeaders = array('alg', 'jku', 'jwk', 'x5u', 'x5t', 'x5c', 'kid', 'typ', 'cty', 'crit');

	protected $algos = array(
		'HS256' => 'sha256',
		'HS384' => 'sha384',
		'HS512' => 'sha512',
	);

	public function setParameter(array $parameter)
	{
		// lower case all keys
		$parameter = array_change_key_case($parameter);

		// add extension keys from the crit header
		$extension = isset($parameter['crit']) ? $parameter['crit'] : null;

		if(!empty($extension) && is_array($extension))
		{
			$this->reservedHeaders = array_merge($this->reservedHeaders, $extension);
		}

		// remove all unreserved header fields
		$parameter = array_intersect_key($parameter, array_flip($this->reservedHeaders));

		parent::setParameter($parameter);
	}

	/**
	 * Sets the algorithm for this websignature. Throws an exception if the 
	 * algorithm is not supported
	 *
	 * @param string
	 */
	public function setAlg($alg)
	{
		if(isset($this->algos[$alg]))
		{
			$this->parameter['alg'] = $alg;
		}
		else
		{
			throw new Exception('Unsupported signature algorithm');
		}
	}

	/**
	 * Validates the given signature using the $key. Returns true if the 
	 * signature is valid
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function validate($key)
	{
		return strcmp($this->signature, $this->getEncodedSignature($key)) === 0;
	}

	/**
	 * Returns this signature in the JWS Compact Serialization format
	 *
	 * @param string $key
	 * @return string
	 */
	public function getCompact($key)
	{
		$this->setHeader(Json::encode($this->parameter));
		$this->setSignature($this->getEncodedSignature($key));

		$header  = $this->base64Encode($this->header);
		$payload = $this->base64Encode($this->payload);

		return $header . '.' . $payload . '.' . $this->signature;
	}

	protected function getEncodedSignature($key)
	{
		$alg = $this->getAlg();

		if(isset($this->algos[$alg]))
		{
			$header    = $this->base64Encode($this->header);
			$payload   = $this->base64Encode($this->payload);

			$data      = $header . '.' . $payload;
			$signature = $this->base64Encode(hash_hmac($this->algos[$alg], $data, $key, true));

			return $signature;
		}
		else
		{
			throw new Exception('Unsupported signature algorithm');
		}
	}
}
