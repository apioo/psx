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

namespace PSX\Oauth\Signature;

use PSX\Oauth;
use PSX\Oauth\SignatureAbstract;

/**
 * HMACSHA1
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class HMACSHA1 extends SignatureAbstract
{
	public function build($baseString, $consumerSecret, $tokenSecret = '')
	{
		$key = Oauth::urlEncode($consumerSecret) . '&' . Oauth::urlEncode($tokenSecret);

		if(function_exists('hash_hmac'))
		{
			$signature = base64_encode(hash_hmac('sha1', $baseString, $key, true));
		}
		else
		{
			$blocksize = 64;

			if(strlen($key) > $blocksize)
			{
				$key = pack('H*', sha1($key));
			}

			$key  = str_pad($key, $blocksize, chr(0x00));
			$ipad = str_repeat(chr(0x36), $blocksize);
			$opad = str_repeat(chr(0x5c), $blocksize);
			$hmac = pack('H*', sha1(($key ^ $opad) . pack('H*', sha1(($key ^ $ipad) . $baseString))));

			$signature = base64_encode($hmac);
		}

		return $signature;
	}
}

