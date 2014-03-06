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

namespace PSX\Dispatch;

use PSX\Http\Response;
use PSX\Http\Stream\TempStream;

/**
 * ResponseFactory
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ResponseFactory implements ResponseFactoryInterface
{
	public function createResponse()
	{
		$scheme   = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
		$response = new Response($scheme);
		$response->addHeader('X-Powered-By', 'psx');
		/*
		$response->addHeader('Expires', 'Thu, 09 Oct 1986 01:00:00 GMT');
		$response->addHeader('Last-Modified', 'Thu, 09 Oct 1986 01:00:00 GMT');
		$response->addHeader('Cache-Control: no-store, no-cache, must-revalidate');
		$response->addHeader('Pragma: no-cache');
		*/
		$response->setBody(new TempStream(fopen('php://memory', 'r+')));

		return $response;
	}
}
