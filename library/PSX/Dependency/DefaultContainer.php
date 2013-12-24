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

namespace PSX\Dependency;

use PSX\Base;
use PSX\Config;
use PSX\Dispatch;
use PSX\Http;
use PSX\Input;
use PSX\Loader;
use PSX\Session;
use PSX\Sql;
use PSX\Template;
use PSX\Validate;
use PSX\Data\Reader;
use PSX\Data\ReaderFactory;
use PSX\Data\Writer;
use PSX\Data\WriterFactory;
use PSX\Domain\DomainManager;
use PSX\Handler\Manager\DefaultManager;
use PSX\Handler\Manager\DatabaseManager;
use PSX\Handler\Manager\HttpManager;
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
	 * @return PSX\Dispatch
	 */
	public function getDispatch()
	{
		return new Dispatch($this->get('config'), $this->get('loader'));
	}

	/**
	 * @return PSX\Http
	 */
	public function getHttp()
	{
		return new Http();
	}

	/**
	 * @return PSX\Input\ContainerInterface
	 */
	public function getInputCookie()
	{
		return new Input\Cookie($this->get('validate'));
	}

	/**
	 * @return PSX\Input\ContainerInterface
	 */
	public function getInputFiles()
	{
		return new Input\Files($this->get('validate'));
	}

	/**
	 * @return PSX\Input\ContainerInterface
	 */
	public function getInputGet()
	{
		return new Input\Get($this->get('validate'));
	}

	/**
	 * @return PSX\Input\ContainerInterface
	 */
	public function getInputPost()
	{
		return new Input\Post($this->get('validate'));
	}

	/**
	 * @return PSX\Input\ContainerInterface
	 */
	public function getInputRequest()
	{
		return new Input\Request($this->get('validate'));
	}

	/**
	 * @return PSX\Loader
	 */
	public function getLoader()
	{
		$loader = new Loader($this);

		// configure loader
		//$loader->addRoute('.well-known/host-meta', 'foo');

		return $loader;
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
	 * @return PSX\Data\ReaderFactory
	 */
	public function getReaderFactory()
	{
		$reader = new ReaderFactory();
		$reader->addReader(new Reader\Json());
		$reader->addReader(new Reader\Form());
		$reader->addReader(new Reader\Gpc());
		$reader->addReader(new Reader\Multipart());
		$reader->addReader(new Reader\Raw());
		$reader->addReader(new Reader\Xml());

		return $reader;
	}

	/**
	 * @return PSX\Data\WriterFactory
	 */
	public function getWriterFactory()
	{
		$writer = new WriterFactory();
		$writer->addWriter(new Writer\Json());
		$writer->addWriter(new Writer\Atom());
		$writer->addWriter(new Writer\Jsonp());
		$writer->addWriter(new Writer\Rss());
		$writer->addWriter(new Writer\Xml());

		return $writer;
	}

	/**
	 * @return PSX\Domain\DomainManagerInterface
	 */
	public function getDomainManager()
	{
		return new DomainManager($this);
	}

	/**
	 * @return Symfony\Component\EventDispatcher\EventDispatcherInterface
	 */
	public function getEventDispatcher()
	{
		return new EventDispatcher();
	}

	/**
	 * @return PSX\Handler\HandlerManagerInterface
	 */
	public function getDefaultManager()
	{
		return new DefaultManager();
	}

	/**
	 * @return PSX\Handler\HandlerManagerInterface
	 */
	public function getDatabaseManager()
	{
		return new DatabaseManager($this->get('sql'));
	}

	/**
	 * @return PSX\Handler\HandlerManagerInterface
	 */
	public function getHttpManager()
	{
		return new HttpManager($this->get('http'));
	}

	/**
	 * @return PSX\Handler\DoctrineManagerInterface
	 */
	/*
	public function getDoctrineManager()
	{
		return new DoctrineManager($this->get('entityManager'));
	}
	*/

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
}

