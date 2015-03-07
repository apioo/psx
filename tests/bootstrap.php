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

$loader = require(__DIR__ . '/../vendor/autoload.php');
$loader->add('PSX', 'tests');

PSX\Bootstrap::setupEnvironment(getContainer()->get('config'));

// some settings for the session test
ini_set('session.use_cookies', 0);
ini_set('session.use_only_cookies', 0);
ini_set('session.use_trans_sid', 1);
ini_set('session.cache_limiter', ''); // prevent sending header

// test hhvm settings
if(getenv('TRAVIS_PHP_VERSION') == 'hhvm')
{
	ini_set('hhvm.libxml.ext_entity_whitelist', 'file');
}

function getContainer()
{
	static $container;

	if($container === null)
	{
		$container = require_once(__DIR__ . '/../container.php');

		setUpConnection($container);
		setUpConfig($container);
	}

	return $container;
}

function hasConnection()
{
	return PSX_CONNECTION === true;
}

function setUpConnection($container)
{
	$params = null;
	switch(getenv('DB'))
	{
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
				'url' => 'sqlite:///:memory:'
			);
			break;
	}

	if(!empty($params))
	{
		try
		{
			$config = new Doctrine\DBAL\Configuration();
			$config->setSQLLogger(new PSX\Sql\Logger($container->get('logger')));

			$connection = Doctrine\DBAL\DriverManager::getConnection($params, $config);
			$fromSchema = $connection->getSchemaManager()->createSchema();

			// we create the schema only if the table does not exist
			if(!$fromSchema->hasTable('psx_cache_handler_sql_test'))
			{
				$toSchema = PSX\Sql\TestSchema::getSchema();
				$queries  = $fromSchema->getMigrateToSql($toSchema, $connection->getDatabasePlatform());

				foreach($queries as $query)
				{
					$connection->query($query);
				}
			}

			$container->set('connection', $connection);

			define('PSX_CONNECTION', true);

			return;
		}
		catch(Doctrine\DBAL\DBALException $e)
		{
		}
	}

	define('PSX_CONNECTION', false);
}

function setUpConfig($container)
{
	$config = $container->get('config');
	$config['psx_url']          = 'http://127.0.0.1';
	$config['psx_dispatch']     = '';
	$config['psx_path_cache']   = 'cache';
	$config['psx_path_library'] = 'library';
}