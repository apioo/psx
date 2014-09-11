<?php

$loader = require('vendor/autoload.php');
$loader->add('PSX', 'tests');

PSX\Bootstrap::setupEnvironment(getContainer()->get('config'));

// some settings for the session test
ini_set('session.use_cookies', 0);
ini_set('session.use_only_cookies', 0);
ini_set('session.use_trans_sid', 1);
ini_set('session.cache_limiter', ''); // prevent sending header

function getContainer()
{
	static $container;

	if($container === null)
	{
		$container = new PSX\Dependency\DefaultContainer();
		$container->setParameter('config.file', 'configuration.php');

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
		catch(PDOException $e)
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
