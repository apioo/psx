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

namespace PSX\Controller;

use DOMDocument;
use PSX\ControllerAbstract;
use PSX\Http;
use PSX\Loader\Location;

/**
 * GenericErrorController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class GenericErrorController extends ControllerAbstract
{
	public function processResponse()
	{
		$exception = $this->location->getParameter(Location::KEY_EXCEPTION);

		if($exception instanceof \Exception)
		{
			$this->handleException($exception);
		}
	}

	protected function handleException(\Exception $e)
	{
		// set status code
		$code = $this->response->getStatusCode();

		if($code === null && isset(Http::$codes[$e->getCode()]))
		{
			$code = $e->getCode();
		}
		else if($code === null)
		{
			$code = 500;
		}

		$this->response->setStatusCode($code);

		// set error template
		if(!$this->getTemplate()->hasFile() && strpos(get_class($this), '\\Application\\') === false)
		{
			$this->getTemplate()->set($this->getFallbackTemplate());
		}

		// build message
		if($this->config['psx_debug'] === true)
		{
			$message = $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
			$trace   = $e->getTraceAsString();
		}
		else
		{
			$message = 'The server encountered an internal error and was unable to complete your request.';
			$trace   = null;
		}

		$data = array(
			'success' => false,
			'message' => $message,
			'trace'   => $trace,
		);

		$this->setBody($data);
	}

	/**
	 * Returns the fallback template which is used if the template has no file
	 * and the controller is not in an application structure
	 *
	 * @return string|Closure
	 */
	protected function getFallbackTemplate()
	{
		if(isset($this->config['psx_error_template']))
		{
			return $this->config['psx_error_template'];
		}
		else
		{
			return function($params){

				return <<<HTML
<!DOCTYPE>
<html>
<head>
	<title>Internal Server Error</title>
	<style type="text/css"><!--
		body { color: #000000; background-color: #FFFFFF; }
		a:link { color: #0000CC; }
		p, address, pre {margin-left: 3em;}
		span {font-size: smaller;}
	--></style>
</head>

<body>
<h1>Internal Server Error</h1>
<p>
	{$params['message']}
</p>
<p>
	<pre>{$params['trace']}</pre>
</p>
</body>
</html>
HTML;

			};
		}
	}
}
