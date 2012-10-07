<?php

require_once('../library/PSX/Config.php');
require_once('../library/PSX/Bootstrap.php');

$config = new PSX_Config('../configuration.php');

$config['psx_path_cache']    = '../cache';
$config['psx_path_library']  = '../library';
$config['psx_path_module']   = '../module';
$config['psx_path_template'] = '../template';

$bootstrap = new PSX_Bootstrap($config, '..');

