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

namespace PSX\Framework\Test;

use Closure;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;
use PSX\Framework\Bootstrap;
use PSX\Framework\Config\Config;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Environment
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Environment
{
    protected static $basePath;
    protected static $container;
    protected static $config;
    protected static $hasConnection = false;

    /**
     * Setups the environment to run unit tests. Includes the DI container and
     * optional creates an database schema
     *
     * @codeCoverageIgnore
     * @param string $basePath
     * @param Closure $schemaSetup
     */
    public static function setup($basePath, Closure $schemaSetup = null)
    {
        self::$basePath = $basePath;

        // setup PHP ini settings
        self::setupIni();

        // setup container
        self::setupContainer();

        // bootstrap PSX environment
        Bootstrap::setupEnvironment(self::getContainer()->get('config'));

        // setup database connection
        self::setupConnection(self::$container, $schemaSetup);
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public static function getContainer()
    {
        return self::$container;
    }

    /**
     * Returns an service from the DI container
     *
     * @param string $service
     * @return mixed
     */
    public static function getService($service)
    {
        return self::$container->get($service);
    }

    /**
     * Returns a clean configuration which has the original values even if an
     * test has modified the config
     *
     * @return \PSX\Framework\Config\Config
     */
    public static function getConfig()
    {
        return new Config(self::$config);
    }

    /**
     * @return boolean
     */
    public static function hasConnection()
    {
        return self::$hasConnection;
    }

    /**
     * @codeCoverageIgnore
     */
    protected static function setupIni()
    {
        ini_set('session.use_cookies', 0);
        ini_set('session.use_only_cookies', 0);
        ini_set('session.use_trans_sid', 1);
        ini_set('session.cache_limiter', ''); // prevent sending header

        if (getenv('TRAVIS_PHP_VERSION') == 'hhvm') {
            ini_set('hhvm.libxml.ext_entity_whitelist', 'file');
        }
    }

    /**
     * @codeCoverageIgnore
     */
    protected static function setupContainer()
    {
        $file = self::$basePath . '/container.php';

        if (!is_file($file)) {
            throw new RuntimeException('The container file "' . $file . '" does not exist');
        }

        self::$container = require_once($file);

        if (!self::$container instanceof ContainerInterface) {
            throw new RuntimeException('The container file "' . $file . '" must return an Symfony\Component\DependencyInjection\ContainerInterface');
        }

        // set test config
        self::$container->set('config', self::buildConfig(self::$container));
    }

    /**
     * @codeCoverageIgnore
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param \Closure $schemaSetup
     */
    protected static function setupConnection(ContainerInterface $container, Closure $schemaSetup = null)
    {
        $params = null;
        switch (getenv('DB')) {
            case 'mysql':
                $params = array(
                    'dbname'   => $container->get('config')->get('psx_sql_db'),
                    'user'     => $container->get('config')->get('psx_sql_user'),
                    'password' => $container->get('config')->get('psx_sql_pw'),
                    'host'     => $container->get('config')->get('psx_sql_host'),
                    'driver'   => 'pdo_mysql',
                );
                break;

            case 'none':
                $params = null;
                break;

            default:
            case 'sqlite':
                $params = array(
                    'url' => 'sqlite::memory:'
                );
                break;
        }

        if (!empty($params)) {
            try {
                $config     = new Configuration();
                $connection = DriverManager::getConnection($params, $config);
                $fromSchema = $connection->getSchemaManager()->createSchema();

                // we get the schema from the callback if available
                if ($schemaSetup !== null) {
                    $toSchema = $schemaSetup($fromSchema, $connection);

                    if ($toSchema instanceof Schema) {
                        $queries = $fromSchema->getMigrateToSql($toSchema, $connection->getDatabasePlatform());

                        foreach ($queries as $query) {
                            $connection->query($query);
                        }
                    }
                }

                $container->set('connection', $connection);

                self::$hasConnection = true;
            } catch (DBALException $e) {
                $container->get('logger')->error($e->getMessage());
            }
        }
    }

    /**
     * @codeCoverageIgnore
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @return \PSX\Framework\Config\Config
     */
    protected static function buildConfig(ContainerInterface $container)
    {
        self::$config = $container->get('config')->getArrayCopy();

        // set an fix url and no dispatch
        self::$config['psx_url']      = 'http://127.0.0.1';
        self::$config['psx_dispatch'] = '';
        self::$config['psx_debug']    = true;

        return new Config(self::$config);
    }
}
