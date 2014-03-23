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

namespace PSX\Handler\Mongodb;

use PSX\Handler\MappingAbstract;
use PSX\Handler\Mongodb\MongodbTestCase;

/**
 * ManagerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ManagerTest extends MongodbTestCase
{
	public function getDataSetFlatXmlFile()
	{
		return dirname(__FILE__) . '/../handler_fixture.xml';
	}

	public function testManager()
	{
		$manager = new Manager($this->getMongoClient());

		$handler = $manager->getHandler('PSX\Handler\Mongodb\TestHandler');

		$this->assertInstanceOf('PSX\Handler\Mongodb\TestHandler', $handler);

		$handler = $manager->getHandler(function($client){
			return new Mapping($client->selectCollection('psx', 'psx_handler_comment'), array(
				'id'     => MappingAbstract::TYPE_INTEGER | 10 | MappingAbstract::ID_PROPERTY,
				'userId' => MappingAbstract::TYPE_INTEGER | 10,
				'title'  => MappingAbstract::TYPE_STRING | 32,
				'date'   => MappingAbstract::TYPE_DATETIME,
			));
		});

		$this->assertInstanceOf('PSX\Handler\Mongodb\CallbackHandler', $handler);
	}

	protected function getHandler()
	{
		return new TestHandler($this->getMongoClient());
	}
}
