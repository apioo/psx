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

		$config = $container->get('config');
		$config['psx_url']          = 'http://127.0.0.1';
		$config['psx_dispatch']     = '';
		$config['psx_path_cache']   = 'cache';
		$config['psx_path_library'] = 'library';

		// check whether an SQL connection is available
		try
		{
			$container->get('connection')->query('SELECT PI()');

			define('PSX_CONNECTION', true);
		}
		catch(\Exception $e)
		{
			define('PSX_CONNECTION', false);
		}
	}

	return $container;
}

function hasConnection()
{
	return PSX_CONNECTION === true;
}
