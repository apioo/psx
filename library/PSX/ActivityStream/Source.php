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

namespace PSX\ActivityStream;

use PSX\Data\RecordAbstract;

/**
 * Source
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Source extends RecordAbstract
{
	protected $objectType;
	protected $displayName;
	protected $url;

	public function getName()
	{
		return 'source';
	}

	public function getFields()
	{
		return array(

			'objectType'  => $this->objectType,
			'displayName' => $this->displayName,
			'url'         => $this->url,

		);
	}

	/**
	 * @param string
	 */
	public function setObjectType($objectType)
	{
		$this->objectType = $objectType;
	}

	/**
	 * @param string
	 */
	public function setDisplayName($displayName)
	{
		$this->displayName = $displayName;
	}

	/**
	 * @param string
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}
}
