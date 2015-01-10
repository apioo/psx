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

namespace PSX\Log;

use Monolog\Formatter\NormalizerFormatter;

/**
 * ErrorFormatter
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ErrorFormatter extends NormalizerFormatter
{
	public function format(array $record)
	{
		$message = $record['channel'] . '.' . $record['level_name'] . ': ';

		if(isset($record['context']['severity']))
		{
			$severity = $this->getSeverity($record['context']['severity']);
			if(!empty($severity))
			{
				$message.= $severity . ': ';
			}
		}

		$message.= $record['message'];

		if(isset($record['context']['file']))
		{
			$message.= ' in ' . $record['context']['file'];
		}

		if(isset($record['context']['line']))
		{
			$message.= ' on line ' . $record['context']['line'];
		}

		if(isset($record['context']['trace']))
		{
			$message.= "\n" . 'Stack trace:' . "\n" . $record['context']['trace'];
		}

		return $message;
	}

	protected function getSeverity($severity)
	{
		switch($severity)
		{
			case E_ERROR:
				return 'PHP Error';

			case E_CORE_ERROR:
				return 'PHP Core error';

			case E_COMPILE_ERROR:
				return 'PHP Compile error';

			case E_USER_ERROR:
				return 'PHP User error';

			case E_RECOVERABLE_ERROR:
				return 'PHP Recoverable error';

			case E_WARNING:
				return 'PHP Warning';

			case E_CORE_WARNING:
				return 'PHP Core warning';

			case E_COMPILE_WARNING:
				return 'PHP Compile warning';

			case E_USER_WARNING:
				return 'PHP User warning';

			case E_PARSE:
				return 'PHP Parse';

			case E_NOTICE:
				return 'PHP Notice';

			case E_USER_NOTICE:
				return 'PHP User notice';

			case E_STRICT:
				return 'PHP Strict';

			case E_DEPRECATED:
				return 'PHP Deprecated';

			case E_USER_DEPRECATED:
				return 'PHP User deprecated';

			default:
				return null;
		}
	}
}
