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

namespace PSX\Api\Documentation;

use PSX\Api\View;

/**
 * Data
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Data
{
	protected $data = array();

	public function set($modifier, $data)
	{
		$this->data[$modifier] = $data;
	}

	public function get($modifier)
	{
		return isset($this->data[$modifier]) ? $this->data[$modifier] : null;
	}

	public function toArray()
	{
		$result  = array();
		$methods = array(
			View::METHOD_GET    => 'get', 
			View::METHOD_POST   => 'post', 
			View::METHOD_PUT    => 'put', 
			View::METHOD_DELETE => 'delete');

		$types   = array(
			View::TYPE_REQUEST  => 'request', 
			View::TYPE_RESPONSE => 'response'
		);

		foreach($methods as $method => $methodName)
		{
			$row = array();

			foreach($types as $type => $typeName)
			{
				$modifier = $method | $type;
				$data     = $this->get($modifier);

				if($data !== null)
				{
					$row[$typeName] = $data;
				}
			}

			if(!empty($row))
			{
				$result[$methodName] = $row;
			}
		}

		return $result;
	}
}
