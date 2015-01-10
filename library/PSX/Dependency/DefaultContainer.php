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

namespace PSX\Dependency;

use Doctrine\Common\Annotations\AnnotationRegistry;
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
use PSX\Http;
use PSX\Log\ErrorFormatter;
use PSX\Session;
use PSX\Sql\Logger as SqlLogger;
use PSX\Sql\TableManager;
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

	/**
	 * @return PSX\Config
	 */
	public function getConfig()
	{
		$config = new Config($this->appendDefaultConfig());
		$config = $config->merge(Config::fromFile($this->getParameter('config.file')));

		return $config;
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
		$config->setSQLLogger(new SqlLogger($this->get('logger')));

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
		$handler = new MonologHandler\ErrorLogHandler(MonologHandler\ErrorLogHandler::OPERATING_SYSTEM, Logger::DEBUG, true, true);
		$handler->setFormatter(new ErrorFormatter());

		$logger = new Logger('psx');
		$logger->pushHandler($handler);

		return $logger;
	}

	/**
	 * @return PSX\Sql\TableManagerInterface
	 */
	public function getTableManager()
	{
		return new TableManager($this->get('connection'));
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

		return EntityManager::create($connection, $config, $connection->getEventManager());
	}

	protected function appendDefaultConfig()
	{
		return array(
			'psx_dispatch'            => 'index.php/',
			'psx_timezone'            => 'UTC',
			'psx_error_controller'    => null,
			'psx_error_template'      => null,
			'psx_annotation_autoload' => array('Doctrine\ORM\Mapping', 'JMS\Serializer\Annotation'),
			'psx_soap_namespace'      => 'http://phpsx.org/2014/data',
		);
	}
}
