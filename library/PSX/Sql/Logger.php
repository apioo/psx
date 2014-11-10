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

namespace PSX\Sql;

use Psr\Log\LoggerInterface;
use Doctrine\DBAL\Logging\SQLLogger;

/**
 * Logger
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @author  Fabien Potencier <fabien@symfony.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Logger implements SQLLogger
{
	const MAX_STRING_LENGTH = 32;
	const BINARY_DATA_VALUE = '(binary value)';

	protected $logger;

	/**
	 * @param Psr\Log\LoggerInterface $logger
	 */
	public function __construct(LoggerInterface $logger)
	{
		$this->logger = $logger;
	}

	public function startQuery($sql, array $params = null, array $types = null)
	{
		if(is_array($params))
		{
			foreach($params as $key => $value)
			{
				// non utf-8 strings break json encoding
				if(!preg_match('//u', $params[$key]))
				{
					$params[$key] = self::BINARY_DATA_VALUE;
					continue;
				}

				// detect if the too long string must be shorten
				if(self::MAX_STRING_LENGTH < strlen($params[$key]))
				{
					$params[$key] = substr($params[$key], 0, self::MAX_STRING_LENGTH - 6).' [...]';
					continue;
				}
			}
		}

		$this->logger->debug($sql, $params === null ? array() : $params);
	}

	public function stopQuery()
	{
	}
}
