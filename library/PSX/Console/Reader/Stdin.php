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

namespace PSX\Console\Reader;

use InvalidArgumentException;
use PSX\Console\ReaderInterface;

/**
 * Helper class which reads the stdin until an EOT character occurs or EOF is 
 * reached
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Stdin implements ReaderInterface
{
	protected $handle;

	/**
	 * Takes the stream on which the reader operates. If no stream is provided
	 * STDIN is used
	 *
	 * @param resource $handle
	 */
	public function __construct($handle = null)
	{
		if($handle === null)
		{
			$this->handle = STDIN;
		}
		else if(is_resource($handle))
		{
			$this->handle = $handle;
		}
		else
		{
			throw new InvalidArgumentException('Must be an resource');
		}
	}

	public function read()
	{
		$body = '';

		while(!feof($this->handle))
		{
			$line = fgets($this->handle);
			$pos  = strpos($line, chr(4));

			if($pos !== false)
			{
				$body.= substr($line, 0, $pos);
				break;
			}

			$body.= $line;
		}

		return $body;
	}
}
