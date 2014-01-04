<?php

$loader = require('vendor/autoload.php');
$loader->add('PSX', 'tests');

$container = getContainer();

PSX\Bootstrap::setupEnvironment($container->get('config'));

function getContainer()
{
	static $container;

	if($container === null)
	{
		$container = new PSX\Dependency\DefaultContainer();
		$container->setParameter('config.file', 'configuration.php');

		$config = $container->get('config');
		$config['psx_path_cache']   = 'cache';
		$config['psx_path_library'] = 'library';
	}

	return $container;
}