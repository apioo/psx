<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

require_once(__DIR__ . '/../vendor/autoload.php');

// To increase the performance of your application you may include this file
// which contains PSX classes which are needed for each request so they must be
// not loaded with the autoloader. The file can be generated through the 
// "generate:bootstrap_cache" command. Note this makes only sense if you are 
// _not_ using an bytecode cache which is included in HHVM or PHP 5.5.0 and 
// later. Also you could dump an optimized autoloader in order to increase 
// autoloading performance with "composer dump-autoload -o"
// require_once(__DIR__ . '/../cache/bootstrap.cache.php');

$container = require_once(__DIR__ . '/../container.php');

PSX\Bootstrap::setupEnvironment($container->get('config'));

$request  = $container->get('request_factory')->createRequest();
$response = $container->get('response_factory')->createResponse();

$container->get('dispatch')->route($request, $response);
