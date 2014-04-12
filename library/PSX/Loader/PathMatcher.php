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

namespace PSX\Loader;

/**
 * PathMatcher
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class PathMatcher
{
	protected $srcPath;

	public function __construct($srcPath)
	{
		$this->srcPath = explode('/', trim($srcPath, '/'));
	}

	public function match($destPath, array &$parameters = array())
	{
		$destPath = explode('/', trim($destPath, '/'));

		if(count($this->srcPath) == count($destPath))
		{
			foreach($destPath as $key => $part)
			{
				if(isset($part[0]) && $part[0] == ':')
				{
					$name = substr($part, 1);

					$parameters[$name] = $this->srcPath[$key];
				}
				else if(isset($part[0]) && $part[0] == '$')
				{
					$pos  = strpos($part, '<');
					$name = substr($part, 1, $pos - 1);
					$rexp = substr($part, $pos + 1, -1);

					if(preg_match('/' . $rexp . '/', $this->srcPath[$key]))
					{
						$parameters[$name] = $this->srcPath[$key];
					}
					else
					{
						return false;
					}
				}
				else if($this->srcPath[$key] == $part)
				{
				}
				else
				{
					return false;
				}
			}

			return true;
		}

		return false;
	}
}
