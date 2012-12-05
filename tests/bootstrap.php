<?php

require_once('library/PSX/Config.php');
require_once('library/PSX/Bootstrap.php');

$bootstrap = new PSX_Bootstrap(getConfig());
$bootstrap->addIncludePath('tests');

function getConfig()
{
	static $config;

	if($config === null)
	{
		$config = new PSX_Config('configuration.php');
		$config['psx_path_cache']    = 'cache';
		$config['psx_path_library']  = 'library';
		$config['psx_path_module']   = 'module';
		$config['psx_path_template'] = 'template';
	}

	return $config;
}
