<?php

require_once('psx/library/PSX/Config.php');
require_once('psx/library/PSX/Bootstrap.php');

$config    = new PSX\Config('psx/configuration.php');
$bootstrap = new PSX\Bootstrap($config);

echo PSX\Base::getVersion();
