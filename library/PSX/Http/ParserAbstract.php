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

namespace PSX\Http;

use InvalidArgumentException;
use RuntimeException;
use PSX\Http;

/**
 * ParserAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ParserAbstract
{
	const MODE_STRICT = 0x1;
	const MODE_LOOSE  = 0x2;

	protected $mode;

	/**
	 * The mode indicates how the header is detected in strict mode we search 
	 * exactly for CRLF CRLF in loose mode we look for the first empty line. In
	 * loose mode we can parse an header wich was defined in the code means is
	 * not strictly seperated by CRLF
	 *
	 * @param integer $mode
	 */
	public function __construct($mode = self::MODE_STRICT)
	{
		$this->mode = $mode;
	}

	/**
	 * Converts an raw http message into an PSX\Http\Message object
	 *
	 * @param string $content
	 * @return PSX\Http\Message
	 */
	abstract public function parse($content);

	/**
	 * Splits an given http message into the header and body part
	 *
	 * @param string $message
	 * @return array
	 */
	protected function splitMessage($message)
	{
		if($this->mode == self::MODE_STRICT)
		{
			$pos    = strpos($message, Http::$newLine . Http::$newLine);
			$header = substr($message, 0, $pos);
			$body   = trim(substr($message, $pos + 1));
		}
		else if($this->mode == self::MODE_LOOSE)
		{
			$lines  = explode("\n", $message);
			$header = '';
			$body   = '';
			$found  = false;
			$count  = count($lines);

			foreach($lines as $i => $line)
			{
				$line = trim($line);

				if(!$found && empty($line))
				{
					$found = true;
					continue;
				}

				if(!$found)
				{
					$header.= $line . Http::$newLine;
				}
				else
				{
					$body.= $line . ($i < $count - 1 ? "\n" : '');
				}
			}
		}
		else
		{
			throw new RuntimeException('Invalid parse mode');
		}

		return array($header, $body);
	}

	/**
	 * @param string $content
	 * @return string
	 */
	protected function normalize($content)
	{
		if(empty($content))
		{
			throw new InvalidArgumentException('Empty message');
		}

		if($this->mode == self::MODE_LOOSE)
		{
			$content = str_replace(array("\r\n", "\n", "\r"), "\n", $content);
		}

		return $content;
	}

	/**
	 * Parses an raw http header string into an Message object
	 *
	 * @param PSX\Http\Message $message
	 * @param string $header
	 * @return array<string, string>
	 */
	protected function headerToArray(Message $message, $header)
	{
		$lines = explode(Http::$newLine, $header);

		foreach($lines as $line)
		{
			$parts = explode(':', $line, 2);

			if(isset($parts[0]) && isset($parts[1]))
			{
				$key   = $parts[0];
				$value = substr($parts[1], 1);

				$message->addHeader($key, $value);
			}
		}
	}

	protected function getStatusLine($message)
	{
		if($this->mode == self::MODE_STRICT)
		{
			$pos = strpos($message, Http::$newLine);
		}
		else if($this->mode == self::MODE_LOOSE)
		{
			$pos = strpos($message, "\n");
		}

		return $pos !== false ? substr($message, 0, $pos) : false;
	}

	public static function buildHeaderFromMessage(Message $message)
	{
		$headers = $message->getHeaders();
		$result  = array();

		foreach($headers as $key => $value)
		{
			if($key == 'set-cookie')
			{
				foreach($value as $cookie)
				{
					$result[] = $key . ': ' . $cookie;
				}
			}
			else
			{
				$result[] = $key . ': ' . implode(', ', $value);
			}
		}

		return $result;
	}
}
