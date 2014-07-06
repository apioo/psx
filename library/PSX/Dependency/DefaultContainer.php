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

namespace PSX\Dependency;

use Monolog\Logger;
use PSX\Base;
use PSX\Config;
use PSX\Http;
use PSX\Session;
use PSX\Sql;
use PSX\Sql\TableManager;
use PSX\Template;
use PSX\Validate;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * DefaultContainer
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DefaultContainer extends Container
{
	use Command;
	use Controller;
	use Data;
	use Handler;

	/**
	 * @return PSX\Base
	 */
	public function getBase()
	{
		return new Base($this->get('config'));
	}

	/**
	 * @return PSX\Config
	 */
	public function getConfig()
	{
		return new Config($this->getParameter('config.file'));
	}

	/**
	 * @return PSX\Http
	 */
	public function getHttp()
	{
		return new Http();
	}

	/**
	 * @return PSX\Session
	 */
	public function getSession()
	{
		$session = new Session($this->getParameter('session.name'));
		$session->start();

		return $session;
	}

	/**
	 * @return PSX\Sql\Connection
	 */
	public function getSql()
	{
		return new Sql($this->get('config')->get('psx_sql_host'),
			$this->get('config')->get('psx_sql_user'),
			$this->get('config')->get('psx_sql_pw'),
			$this->get('config')->get('psx_sql_db'));
	}

	/**
	 * @return PSX\Sql\TableManager
	 */
	public function getTableManager()
	{
		return new TableManager($this->get('sql'));
	}

	/**
	 * @return PSX\TemplateInterface
	 */
	public function getTemplate()
	{
		return new Template();
	}

	/**
	 * @return PSX\Validate
	 */
	public function getValidate()
	{
		return new Validate();
	}

	/**
	 * @return PSX\Dependency\ObjectBuilderInterface
	 */
	public function getObjectBuilder()
	{
		return new ObjectBuilder($this);
	}

	/**
	 * @return Symfony\Component\EventDispatcher\EventDispatcherInterface
	 */
	public function getEventDispatcher()
	{
		return new EventDispatcher();
	}

	/**
	 * @return Psr\Log\LoggerInterface
	 */
	public function getLogger()
	{
		$logger = new Logger('psx');
		//$logger->pushHandler(new StreamHandler('path/to/your.log', Logger::WARNING));

		return $logger;
	}

	/**
	 * @return Doctrine\ORM\EntityManager
	 */
	/*
	public function getEntityManager()
	{
		$paths     = array(PSX_PATH_LIBRARY);
		$isDevMode = $this->get('config')->get('psx_debug');
		$dbParams  = array(
			'driver'   => 'pdo_mysql',
			'user'     => $this->get('config')->get('psx_sql_user'),
			'password' => $this->get('config')->get('psx_sql_pw'),
			'dbname'   => $this->get('config')->get('psx_sql_db'),
		);

		$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
		$config->addCustomHydrationMode(RecordHydrator::HYDRATE_RECORD, 'PSX\Handler\Doctrine\RecordHydrator');

		$entityManager = EntityManager::create($dbParams, $config);

		return $entityManager;
	}
	*/

	/**
	 * @return MongoClient
	 */
	/*
	public function getMongoClient()
	{
		return new MongoClient();
	}
	*/
}
