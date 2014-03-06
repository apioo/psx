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

namespace PSX\Oauth;

use PSX\Oauth;

/**
 * SignatureAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class SignatureAbstract
{
	/**
	 * Creates a signature from the base string with the consumer secret
	 * as key. If the token secret is avialable it is append to the key.
	 * Returns the base64 encoded signature
	 *
	 * @see http://oauth.net/core/1.0a#rfc.section.9
	 * @param string $baseString
	 * @param string $consumerSecret
	 * @param string $tokenSecret
	 * @return string
	 */
	abstract public function build($baseString, $consumerSecret, $tokenSecret = '');

	/**
	 * Compares whether the $signature is valid by creating a new signature
	 * and comparing them with $signature
	 *
	 * @param string $baseString
	 * @param string $consumerSecret
	 * @param string $tokenSecret
	 * @param string $signature
	 * @return boolean
	 */
	public function verify($baseString, $consumerSecret, $tokenSecret = '', $signature)
	{
		$lft = Oauth::urlDecode($signature);
		$rgt = Oauth::urlDecode($this->build($baseString, $consumerSecret, $tokenSecret));

		return strcasecmp($lft, $rgt) == 0;
	}
}

