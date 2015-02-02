<?php

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