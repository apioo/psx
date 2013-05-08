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

namespace PSX\Http;

/**
 * Message
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Message
{
	protected $header;
	protected $body;

	/**
	 * __construct
	 *
	 * @param array $header
	 * @param string $body
	 */
	public function __construct(array $header = array(), $body = null)
	{
		$this->setHeader($header);
		$this->setBody($body);
	}

	/**
	 * Sets the message header
	 *
	 * @param array $header
	 * @return void
	 */
	public function setHeader(array $header)
	{
		$this->header = $header;
	}

	/**
	 * Sets the message body
	 *
	 * @param string $body
	 * @return void
	 */
	public function setBody($body)
	{
		$this->body = (string) $body;
	}

	/**
	 * Returns the complete header array if key is null or the specific header
	 * value if available
	 *
	 * @param string $key
	 * @return array|string
	 */
	public function getHeader($key = null)
	{
		if($key !== null)
		{
			$key = strtolower($key);

			return isset($this->header[$key]) ? $this->header[$key] : null;
		}
		else
		{
			return $this->header;
		}
	}

	/**
	 * Returns the message body
	 *
	 * @return string
	 */
	public function getBody()
	{
		return $this->body;
	}
}

