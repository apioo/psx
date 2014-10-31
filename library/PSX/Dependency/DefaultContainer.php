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

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Monolog\Handler as MonologHandler;
use Monolog\Processor as MonologProcessor;
use PSX\Base;
use PSX\Cache;
use PSX\Config;
use PSX\Handler\Doctrine\RecordHydrator;
use PSX\Http;
use PSX\Session;
use PSX\Template;
use PSX\Validate;

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
	use Console;
	use Controller;
	use Data;
	use Event;
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
		$name    = $this->hasParameter('session.name') ? $this->getParameter('session.name') : 'psx';
		$session = new Session($name);

		if(PHP_SAPI != 'cli')
		{
			$session->start();
		}

		return $session;
	}

	/**
	 * @return Doctrine\DBAL\Connection
	 */
	public function getConnection()
	{
		$config = new Configuration();
		$params = array(
			'dbname'   => $this->get('config')->get('psx_sql_db'),
			'user'     => $this->get('config')->get('psx_sql_user'),
			'password' => $this->get('config')->get('psx_sql_pw'),
			'host'     => $this->get('config')->get('psx_sql_host'),
			'driver'   => 'pdo_mysql',
		);

		return DriverManager::getConnection($params, $config);
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
	 * @return Psr\Cache\CacheItemPoolInterface
	 */
	public function getCache()
	{
		return new Cache();
	}

	/**
	 * @return Psr\Log\LoggerInterface
	 */
	public function getLogger()
	{
		$logger = new Logger('psx');
		$logger->pushHandler(new MonologHandler\NullHandler());

		return $logger;
	}

	/**
	 * @return Doctrine\ORM\EntityManager
	 */
	public function getEntityManager()
	{
		$connection = $this->get('connection');
		$paths      = array(PSX_PATH_LIBRARY);
		$isDevMode  = $this->get('config')->get('psx_debug');

		$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
		$config->addCustomHydrationMode(RecordHydrator::HYDRATE_RECORD, 'PSX\Handler\Doctrine\RecordHydrator');

		return EntityManager::create($connection, $config, $connection->getEventManager());
	}

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
