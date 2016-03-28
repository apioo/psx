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

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/TestSchema.php');

\PSX\Framework\Test\Environment::setup(__DIR__ . '/..', function(\Doctrine\DBAL\Schema\Schema $fromSchema){

	// create the database schema if not available
	if (!$fromSchema->hasTable('psx_cache_handler_sql_test')) {
		return PSX\TestSchema::getSchema();
	}

});

