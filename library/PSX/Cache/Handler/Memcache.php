<?php
/*
 *  $Id: Memcache.php 637 2012-09-01 10:33:34Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

/**
 * PSX_Cache_Handler_Memcache
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Cache
 * @version    $Revision: 637 $
 */
class PSX_Cache_Handler_Memcache implements PSX_Cache_HandlerInterface
{
	private $memcache;

	public function __construct(Memcache $memcache)
	{
		$this->memcache = $memcache;
	}

	public function load($key)
	{
		$content = $this->memcache->get($key);

		if($content !== false)
		{
			return new PSX_Cache_Item($content, null);
		}
		else
		{
			return false;
		}
	}

	public function write($key, $content, $expire)
	{
		$this->memcache->set($key, $content, 0, $expire);
	}

	public function remove($key)
	{
		$this->memcache->delete($key);
	}

	public function getMemcache()
	{
		return $this->memcache;
	}
}
