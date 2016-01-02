<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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
