<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

$container = require_once(__DIR__ . '/../container.php');

$engine      = new \PSX\Engine\WebServer\Engine($container->getParameter('psx_url'));
$dispatcher  = $container->get(\PSX\Engine\DispatchInterface::class);
$environment = new \PSX\Framework\Environment\Environment($dispatcher, $engine);

return $environment->serve();
