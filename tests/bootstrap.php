<?php

$loader = require('vendor/autoload.php');
$loader->add('PSX', 'tests');

$container = getContainer();

PSX\Bootstrap::setupEnvironment($container->get('config'));

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

		// set void logger
		$logger = new Monolog\Logger('psx');
		$logger->pushHandler(new Monolog\Handler\NullHandler());

		$container->set('logger', $logger);
	}

	return $container;
}