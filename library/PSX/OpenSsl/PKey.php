<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\OpenSsl;

use InvalidArgumentException;

/**
 * PKey
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class PKey
{
	use ErrorHandleTrait;

	protected $res;

	public function __construct($configargs = array())
	{
		if(is_array($configargs))
		{
			$res = openssl_pkey_new($configargs);

			self::handleReturn($res);

			$this->res = $res;
		}
		else if(is_resource($configargs))
		{
			$this->res = $configargs;
		}
		else
		{
			throw new InvalidArgumentException('Must be either an array or a resource');
		}
	}

	public function free()
	{
		openssl_pkey_free($this->res);
	}

	public function getDetails()
	{
		$details = openssl_pkey_get_details($this->res);

		self::handleReturn($details);

		return $details;
	}

	public function getPublicKey()
	{
		$details = $this->getDetails();

		return isset($details['key']) ? $details['key'] : null;
	}

	public function getResource()
	{
		return $this->res;
	}

	public function export(&$out, $passphrase = null, array $configargs = array())
	{
		$result = openssl_pkey_export($this->res, $out, $passphrase, $configargs);

		self::handleReturn($result);

		return $result;
	}

	public static function getPrivate($key, $passphrase = null)
	{
		$res = openssl_pkey_get_private($key, $passphrase);

		self::handleReturn($res);

		return new self($res);
	}

	public static function getPublic($certificate)
	{
		$res = openssl_pkey_get_public($certificate);

		self::handleReturn($res);

		return new self($res);
	}
}
