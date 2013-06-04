<?php

$loader = require_once('vendor/autoload.php');
$loader->add('PSX', 'tests');

$bootstrap = new PSX\Bootstrap(getConfig());

function getConfig()
{
	static $config;

	if($config === null)
	{
		$config = new PSX\Config('configuration.php');
		$config['psx_path_cache']    = 'cache';
		$config['psx_path_library']  = 'library';
		$config['psx_path_module']   = 'module';
		$config['psx_path_template'] = 'template';
	}

	return $config;
}
