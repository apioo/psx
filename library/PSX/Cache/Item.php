<?php
/*
 *  $Id: Item.php 636 2012-09-01 10:32:42Z k42b3.x@googlemail.com $
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
 * PSX_Cache_Item
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Cache
 * @version    $Revision: 636 $
 */
class PSX_Cache_Item
{
	protected $content;
	protected $time;

	public function __construct($content, $time)
	{
		$this->content = $content;
		$this->time    = $time;
	}

	/**
	 * The cached content
	 *
	 * @return mixed
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * The timstamp of the cache item. If the time is null the cache class will
	 * not check whether the cache is expired. In this case this must be done
	 * by the handler
	 *
	 * @return integer
	 */
	public function getTime()
	{
		return $this->time;
	}

	public function __toString()
	{
		return $this->getContent();
	}
}
