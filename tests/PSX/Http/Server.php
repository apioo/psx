<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Http;

use PSX\Http;

/**
 * Test HTTP server script wich responses to scripts in order to test the HTTP
 * sending of handlers
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
