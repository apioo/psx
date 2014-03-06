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

namespace PSX\Handler\Map;

use PSX\Handler\MapHandlerAbstract;
use PSX\Handler\MappingAbstract;

/**
 * TestHandler
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TestHandler extends MapHandlerAbstract
{
	public function getMapping()
	{
		return new Mapping($this->getArray(), array(
			'id'     => MappingAbstract::TYPE_INTEGER | 10 | MappingAbstract::ID_PROPERTY,
			'userId' => MappingAbstract::TYPE_INTEGER | 10,
			'title'  => MappingAbstract::TYPE_STRING | 32,
			'date'   => MappingAbstract::TYPE_DATETIME,
		));
	}

	protected function getArray()
	{
		return array(
			array(
				'id'     => 1,
				'userId' => 1,
				'title'  => 'foo',
				'date'   => '2013-04-29 16:56:32',
			),
			array(
				'id'     => 2,
				'userId' => 1,
				'title'  => 'bar',
				'date'   => '2013-04-29 16:56:32',
			),
			array(
				'id'     => 3,
				'userId' => 2,
				'title'  => 'test',
				'date'   => '2013-04-29 16:56:32',
			),
			array(
				'id'     => 4,
				'userId' => 3,
				'title'  => 'blub',
				'date'   => '2013-04-29 16:56:32',
			),
		);
	}
}
