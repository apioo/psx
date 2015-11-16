<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Dependency;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use PSX\Cache;
use PSX\Config;
use PSX\Exception;
use PSX\Http;
use PSX\Log\LoggerFactory;
use PSX\Session;
use PSX\Sql\Logger as SqlLogger;
use PSX\Sql\TableManager;
use PSX\Template;
use PSX\Validate;

/**
 * DefaultContainer
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
     * @return \PSX\Config
     */
    public function getConfig()
    {
        $config = new Config($this->appendDefaultConfig());
        $config = $config->merge(Config::fromFile($this->getParameter('config.file')));

        return $config;
    }

    /**
     * @return \PSX\Http
     */
    public function getHttp()
    {
        return new Http();
    }

    /**
     * @return \PSX\Session
     */
    public function getSession()
    {
        $name    = $this->hasParameter('session.name') ? $this->getParameter('session.name') : 'psx';
        $session = new Session($name);

        if (PHP_SAPI != 'cli') {
            $session->start();
        }

        return $session;
    }

    /**
     * @return \Doctrine\DBAL\Connection
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
     * @return \PSX\TemplateInterface
     */
    public function getTemplate()
    {
        return new Template();
    }

    /**
     * @return \PSX\Validate
     */
    public function getValidate()
    {
        return new Validate();
    }

    /**
     * @return \PSX\Dependency\ObjectBuilderInterface
     */
    public function getObjectBuilder()
    {
        return new ObjectBuilder($this);
    }

    /**
     * @return \PSX\Exception\ConverterInterface
     */
    public function getExceptionConverter()
    {
        return new Exception\Converter($this->get('config')->get('psx_debug'));
    }

    /**
     * @return \PSX\Cache\CacheItemPoolInterface
     */
    public function getCache()
    {
        return new Cache();
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return LoggerFactory::factory(
            $this->get('config')->get('psx_log_level'),
            $this->get('config')->get('psx_log_handler'),
            $this->get('config')->get('psx_log_uri')
        );
    }

    /**
     * @return \PSX\Sql\TableManagerInterface
     */
    public function getTableManager()
    {
        return new TableManager($this->get('connection'));
    }

    /**
     * @return \Doctrine\ORM\EntityManager
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
            'psx_json_namespace'      => 'urn:schema.phpsx.org#',
            'psx_log_level'           => \Monolog\Logger::ERROR,
            'psx_log_handler'         => 'system',
            'psx_log_uri'             => null,
        );
    }
}
