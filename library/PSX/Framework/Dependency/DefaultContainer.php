<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Framework\Dependency;

use Doctrine\Common\Annotations;
use Doctrine\Common\Cache as DoctrineCache;
use Doctrine\DBAL;
use Doctrine\ORM;
use PSX\Cache;
use PSX\Data\Configuration;
use PSX\Data\Processor;
use PSX\Framework\Log\LogListener;
use PSX\Framework\Template;
use PSX\Http;
use PSX\Framework\Log\LoggerFactory;
use PSX\Schema\SchemaManager;
use PSX\Sql\Logger as SqlLogger;
use PSX\Sql\TableManager;
use PSX\Validate\Validate;
use PSX\Data\Writer;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * DefaultContainer
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DefaultContainer extends Container
{
    use Framework;
    use Console;

    /**
     * @return \Doctrine\Common\Annotations\Reader
     */
    public function getAnnotationReader()
    {
        $reader = new Annotations\SimpleAnnotationReader();
        $reader->addNamespace('PSX\\Api\\Annotation');
        $reader->addNamespace('PSX\\Schema\\Parser\\Popo\\Annotation');
        $reader->addNamespace('PSX\\Framework\\Annotation');

        if (!$this->get('config')->get('psx_debug')) {
            $reader = new Annotations\CachedReader(
                $reader,
                $this->newDoctrineCacheImpl('annotations/psx'),
                $this->get('config')->get('psx_debug')
            );
        }

        return $reader;
    }

    /**
     * @return \Psr\Cache\CacheItemPoolInterface
     */
    public function getCache()
    {
        $handler = new DoctrineCache\FilesystemCache($this->get('config')->get('psx_path_cache'));

        return new Cache\Pool($handler);
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        $config = new DBAL\Configuration();
        $config->setSQLLogger(new SqlLogger($this->get('logger')));

        $params = array(
            'dbname'   => $this->get('config')->get('psx_sql_db'),
            'user'     => $this->get('config')->get('psx_sql_user'),
            'password' => $this->get('config')->get('psx_sql_pw'),
            'host'     => $this->get('config')->get('psx_sql_host'),
            'driver'   => 'pdo_mysql',
        );

        return DBAL\DriverManager::getConnection($params, $config);
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        $eventDispatcher = new EventDispatcher();

        $this->appendDefaultListener($eventDispatcher);

        return $eventDispatcher;
    }

    /**
     * @return \PSX\Http\ClientInterface
     */
    public function getHttpClient()
    {
        return new Http\Client();
    }

    /**
     * @return \PSX\Data\Processor
     */
    public function getIo()
    {
        $config = Configuration::createDefault(
            $this->get('annotation_reader'),
            $this->get('cache'),
            $this->get('config')->get('psx_debug'),
            $this->get('config')->get('psx_soap_namespace')
        );

        $processor = new Processor($config);
        $processor->getConfiguration()->getWriterFactory()->addWriter(new Writer\Html($this->get('template'), $this->get('reverse_router')), 40);

        return $processor;
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
     * @return \PSX\Schema\SchemaManagerInterface
     */
    public function getSchemaManager()
    {
        return new SchemaManager();
    }

    /**
     * @return \PSX\Sql\TableManagerInterface
     */
    public function getTableManager()
    {
        return new TableManager($this->get('connection'));
    }

    /**
     * @return \PSX\Validate\Validate
     */
    public function getValidate()
    {
        return new Validate();
    }

    protected function appendDefaultConfig()
    {
        return array(
            'psx_dispatch'            => 'index.php/',
            'psx_timezone'            => 'UTC',
            'psx_error_controller'    => null,
            'psx_error_template'      => null,
            'psx_annotation_autoload' => [
                'PSX\Api\Annotation',
                'PSX\Schema\Parser\Popo\Annotation',
                'PSX\Framework\Annotation',
                'JMS\Serializer\Annotation',
                'Doctrine\ORM\Mapping'
            ],
            'psx_entity_paths'        => [],
            'psx_soap_namespace'      => 'http://phpsx.org/2014/data',
            'psx_json_namespace'      => 'urn:schema.phpsx.org#',
            'psx_log_level'           => \Monolog\Logger::ERROR,
            'psx_log_handler'         => 'system',
            'psx_log_uri'             => null,
            'psx_filter_pre'          => [],
            'psx_filter_post'         => [],
        );
    }

    protected function appendDefaultListener(EventDispatcherInterface $eventDispatcher)
    {
        $eventDispatcher->addSubscriber(new LogListener($this->get('logger'), $this->get('config')->get('psx_debug')));
    }

    /**
     * If you want to change the doctrine cache which is used in various 
     * components you can override this method
     */
    protected function newDoctrineCacheImpl($namespace)
    {
        return new DoctrineCache\FilesystemCache(PSX_PATH_CACHE . '/' . $namespace);
    }
}
