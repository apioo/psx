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

namespace PSX\Handler\Doctrine;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use PSX\Handler\Doctrine\RecordHydrator;
use PSX\Handler\HandlerTestCase;
use PSX\Sql\DbTestCase;

/**
 * CallbackHandlerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CallbackHandlerTest extends DoctrineTestCase
{
	use HandlerTestCase;

	public function getDataSet()
	{
		return $this->createFlatXMLDataSet(dirname(__FILE__) . '/../handler_fixture.xml');
	}

	protected function getHandler()
	{
		return new CallbackHandler($this->getEntityManager(), function($manager){
			return $manager->createQueryBuilder()
				->from('PSX\Handler\Doctrine\TestEntity', 'comment');
		});
	}
}
