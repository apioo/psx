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

namespace PSX\Module\Foo\Application;

use PSX\Data\Record;
use PSX\Data\RecordAbstract;
use PSX\Module\ApiAbstract;

/**
 * TestApiModule
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TestApiModule extends ApiAbstract
{
	/**
	 * @httpMethod GET
	 * @path /
	 */
	public function doIndex()
	{
		$record = new Record('foo', array('bar' => 'foo'));

		$this->setResponse($record, 'PSX\Module\NoContentTypeJsonWriter', null);
	}

	/**
	 * @httpMethod POST
	 * @path /
	 */
	public function doInsert()
	{
		$record = new NewsRecord();
		$record = $this->import($record);

		$this->setResponse($record, 'PSX\Module\NoContentTypeJsonWriter', null);
	}
}

class NewsRecord extends RecordAbstract
{
	protected $title;
	protected $user;

	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param string $user
	 */
	public function setUser($user)
	{
		$this->user = $user;
	}
	
	public function getUser()
	{
		return $this->user;
	}
}
