<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Http\Handler;

use PSX\Http;
use PSX\Http\HandlerInterface;
use PSX\Http\Request;

/**
 * Callback
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Callback implements HandlerInterface
{
	private $callback;

	private $lastError;
	private $request;
	private $response;

	public function __construct($callback)
	{
		$this->callback = $callback;
	}

	public function request(Request $request)
	{
		try
		{
			$this->request  = $request;
			$this->response = call_user_func($this->callback, $this->request);

			return $this->response;
		}
		catch(\PHPUnit_Framework_Exception $e)
		{
			throw $e;
		}
		catch(\ErrorException $e)
		{
			throw $e;
		}
		catch(\Exception $e)
		{
			$this->lastError = $e->getMessage();
		}
	}

	public function getLastError()
	{
		return $this->lastError;
	}

	public function getRequest()
	{
		return $this->request;
	}

	public function getResponse()
	{
		return $this->response;
	}
}

