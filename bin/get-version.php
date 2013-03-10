<?php

require_once('../library/PSX/Config.php');
require_once('../library/PSX/Bootstrap.php');

$config    = new PSX\Config('../configuration.php');
$bootstrap = new PSX\Bootstrap($config);

echo PSX\Base::getVersion();

