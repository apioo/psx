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

namespace PSX\Handler;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use PSX\Sql\DbTestCase;
use PSX\Handler\Doctrine\RecordHydrator;

/**
 * DoctrineHandlerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DoctrineHandlerTest extends DbTestCase
{
	use HandlerTestCase;

	private static $em;

	public function setUp()
	{
		if(!class_exists('Doctrine\ORM\EntityManager'))
		{
			$this->markTestSkipped('Doctrine not installed');
		}

		parent::setUp();
	}

	public function getDataSet()
	{
		return $this->createFlatXMLDataSet(dirname(__FILE__) . '/handler_fixture.xml');
	}

	protected function getHandler()
	{
		return new Doctrine\TestHandler($this->getEntityManager());
	}

	protected function getEntityManager()
	{
		if(self::$em === null)
		{
			// the default di container doesnt have the entity manager service
			$paths     = array(PSX_PATH_LIBRARY);
			$isDevMode = getContainer()->get('config')->get('psx_debug');
			$dbParams  = array(
				'driver'   => 'pdo_mysql',
				'user'     => getContainer()->get('config')->get('psx_sql_user'),
				'password' => getContainer()->get('config')->get('psx_sql_pw'),
				'dbname'   => getContainer()->get('config')->get('psx_sql_db'),
			);

			$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
			$config->addCustomHydrationMode(RecordHydrator::HYDRATE_RECORD, 'PSX\Handler\Doctrine\RecordHydrator');

			self::$em = EntityManager::create($dbParams, $config);
		}

		return self::$em;
	}
}
