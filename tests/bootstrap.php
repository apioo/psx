<?php

$loader = require_once('vendor/autoload.php');
$loader->add('PSX', 'tests');

$container = getContainer();
$bootstrap = new PSX\Bootstrap($container->get('config'));

function getContainer()
{
	static $container;

	if($container === null)
	{
		$container = new PSX\Dependency\Container();
		$container->setParameter('config.file', 'configuration.php');

		$config = $container->get('config');
		$config['psx_path_cache']    = 'cache';
		$config['psx_path_library']  = 'library';
		$config['psx_path_module']   = 'module';
		$config['psx_path_template'] = 'template';
	}

	return $container;
}