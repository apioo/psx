<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Http;

use PSX\Http;

/**
 * Test HTTP server script wich responses to scripts in order to test the HTTP
 * sending of handlers
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Server
{
	public static function run()
	{
		$path = $_SERVER['REQUEST_URI'];

		if($path == '/head')
		{
			header('Content-Type: application/json');
		}
		else if($path == '/get')
		{
			header('Content-Type: application/json');

			echo json_encode(array(
				'success' => $_SERVER['REQUEST_METHOD'] == 'GET',
				'method'  => $_SERVER['REQUEST_METHOD'],
			));
		}
		else if($path == '/post')
		{
			header('Content-Type: application/json');

			echo json_encode(array(
				'success' => $_SERVER['REQUEST_METHOD'] == 'POST',
				'method'  => $_SERVER['REQUEST_METHOD'],
				'request' => file_get_contents('php://input'),
			));
		}
		else if($path == '/put')
		{
			header('Content-Type: application/json');

			echo json_encode(array(
				'success' => $_SERVER['REQUEST_METHOD'] == 'PUT',
				'method'  => $_SERVER['REQUEST_METHOD'],
				'request' => file_get_contents('php://input'),
			));
		}
		else if($path == '/delete')
		{
			header('Content-Type: application/json');

			echo json_encode(array(
				'success' => $_SERVER['REQUEST_METHOD'] == 'DELETE',
				'method'  => $_SERVER['REQUEST_METHOD'],
				'request' => file_get_contents('php://input'),
			));
		}
		else if($path == '/redirect')
		{
			header('Location: /redirect/one');
		}
		else if($path == '/redirect/one')
		{
			header('Location: /redirect/two');
		}
		else if($path == '/redirect/two')
		{
			header('Content-Type: application/json');

			echo json_encode(array(
				'success' => $_SERVER['REQUEST_METHOD'] == 'GET',
				'method'  => $_SERVER['REQUEST_METHOD'],
			));
		}
		else if($path == '/bigdata')
		{
			header('Content-Type: text/plain');

			// send 1mb
			echo str_repeat('..........', 100000);
		}
		else if($path == '/timeout')
		{
			header('Content-Type: text/plain');

			sleep(8);
		}

		exit;
	}
}

Server::run();
