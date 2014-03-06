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

namespace PSX\Oauth\Provider\Data;

use InvalidArgumentException;
use PSX\Data\InvalidDataException;
use PSX\Data\RecordInterface;
use PSX\Data\Record\ImporterInterface;
use PSX\Http\Message;
use PSX\Oauth;

/**
 * RequestImporter
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RequestImporter implements ImporterInterface
{
	protected $requiredFields;
	protected $map = array(
		'consumerKey'     => 'consumer_key',
		'token'           => 'token',
		'signatureMethod' => 'signature_method',
		'signature'       => 'signature',
		'timestamp'       => 'timestamp',
		'nonce'           => 'nonce',
		'callback'        => 'callback',
		'version'         => 'version',
		'verifier'        => 'verifier'
	);

	public function import($record, $data)
	{
		if(!$data instanceof Message)
		{
			throw new InvalidArgumentException('Data must be an Message object');
		}

		$auth = (string) $data->getHeader('Authorization');

		if(!empty($auth))
		{
			if(strpos($auth, 'OAuth') !== false)
			{
				// get oauth data
				$data  = array();
				$items = explode(',', $auth);

				foreach($items as $v)
				{
					$v = trim($v);

					if(substr($v, 0, 6) == 'oauth_')
					{
						$pair = explode('=', $v);

						if(isset($pair[0]) && isset($pair[1]))
						{
							$key = substr(strtolower($pair[0]), 6);
							$val = trim($pair[1], '"');

							$data[$key] = Oauth::urlDecode($val);
						}
					}
				}

				// check whether all required values are available
				foreach($this->map as $k => $v)
				{
					if(isset($data[$v]))
					{
						$method = 'set' . ucfirst($k);

						if(method_exists($record, $method))
						{
							$record->$method($data[$v]);
						}
						else
						{
							throw new InvalidDataException('Unknown parameter');
						}
					}
					else if(in_array($k, $this->requiredFields))
					{
						throw new InvalidDataException('Required parameter "' . $v . '" is missing');
					}
				}
			}
			else
			{
				throw new InvalidDataException('Unknown OAuth authentication');
			}
		}
		else
		{
			throw new InvalidDataException('Missing Authorization header');
		}
	}

	public function setRequiredFields(array $requiredFields)
	{
		$this->requiredFields = $requiredFields;
	}
}
