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

namespace PSX;

use PSX\Http;
use PSX\Http\Request;
use PSX\Http\Response;

/**
 * Dispatch
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Dispatch extends \Exception
{
	protected $config;
	protected $base;
	protected $loader;

	public function __construct(Config $config, Loader $loader)
	{
		$this->config = $config;
		$this->loader = $loader;
	}

	public function route(Request $request)
	{
		$controller = null;

		ob_start();

		try
		{
			// load controller
			$controller = $this->loader->load($this->getPath());

			// get output
			$content = ob_get_contents();

			// process response
			$body = $controller->processResponse($content);
		}
		catch(\Exception $e)
		{
			$code = Base::getResponseCode();

			if($code === null && isset(Http::$codes[$e->getCode()]))
			{
				$code = $e->getCode();
			}
			else if($code === null)
			{
				$code = 500;
			}

			$accept  = Base::getRequestHeader('Accept');
			$message = $e->getMessage();
			$trace   = '';

			if($this->config['psx_debug'] === true)
			{
				$message.= ' in ' . $e->getFile() . ' on line ' . $e->getLine();
				$trace   = $e->getTraceAsString();
			}

			// build response
			if(strpos($accept, 'text/html') !== false)
			{
				Base::setResponseCode($code);
				header('Content-type: text/html');

				$body = $this->getErrorTemplate($message, $trace);
			}
			else
			{
				Base::setResponseCode($code);
				header('Content-type: text/plain');

				$body = $message . "\n" . $trace;
			}

			// logging
			Log::error($e->getMessage() . "\n" . 'Stack trace:' . "\n" . $e->getTraceAsString() . "\n");
		}

		ob_end_clean();

		// build response
		$response = $this->buildResponse($body);

		// call response filter
		if($controller instanceof ModuleAbstract)
		{
			$filters = $controller->getResponseFilter();

			foreach($filters as $filter)
			{
				$filter->handle($response);
			}
		}

		return $response;
	}

	protected function getPath()
	{
		$default = $this->config['psx_module_default'];
		$input   = $this->config['psx_module_input'];
		$length  = $this->config['psx_module_input_length'];

		if(!empty($input))
		{
			$x = $input;
		}
		else
		{
			$x = $default;
		}

		if(strpos($x, '..') !== false)
		{
			throw new PSX\Exception('Invalid signs in input');
		}

		if($length != 0)
		{
			if(strlen($x) > $length)
			{
				throw new PSX\Exception('Max length of input is ' . $length, 414);
			}
		}

		return $x;
	}

	protected function buildResponse($body)
	{
		$code    = Base::getResponseCode();
		$message = $code === null ? Http::$codes[200] : Http::$codes[$code];
		$header  = Response::headerToArray(implode(Http::$newLine, headers_list()));

		return new Response(Base::getProtocol(), Base::getResponseCode(), $message, $header, $body);
	}

	protected function getErrorTemplate($message, $trace)
	{
		ob_start();

		include PSX_PATH_TEMPLATE . '/' . $this->config['psx_template_dir'] . '/error.tpl';

		$template = ob_get_contents();

		ob_end_clean();

		return $template;
	}
}
