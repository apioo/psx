<?php

require_once('../library/PSX/Config.php');
require_once('../library/PSX/Bootstrap.php');

$config    = new PSX_Config('../configuration.php');
$bootstrap = new PSX_Bootstrap($config);

echo PSX_Base::getVersion();

