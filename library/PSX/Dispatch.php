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

use DOMDocument;
use PSX\Base;
use PSX\ControllerInterface;
use PSX\Dispatch\SenderInterface;
use PSX\Dispatch\RedirectException;
use PSX\Http\Request;
use PSX\Http\Response;

/**
 * The dispatcher routes the request to the fitting controller. The route method
 * contains the global try catch for the application
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Dispatch extends \Exception
{
	protected $config;
	protected $loader;
	protected $sender;

	public function __construct(Config $config, LoaderInterface $loader, SenderInterface $sender)
	{
		$this->config = $config;
		$this->loader = $loader;
		$this->sender = $sender;
	}

	public function route(Request $request, Response $response)
	{
		// load controller
		try
		{
			$this->loader->load($request, $response);
		}
		catch(RedirectException $e)
		{
			$response->setStatusCode($e->getStatusCode());
			$response->setHeader('Location', $e->getUrl());
		}
		catch(\Exception $e)
		{
			$this->handleException($request, $response, $e);
		}

		// send response
		$this->sender->send($response);
	}

	protected function handleException(Request $request, Response $response, \Exception $e)
	{
		// set status code
		$code = $response->getStatusCode();

		if($code === null && isset(Http::$codes[$e->getCode()]))
		{
			$code = $e->getCode();
		}
		else if($code === null)
		{
			$code = 500;
		}

		$response->setStatusCode($code);

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

		// build response
		$accept  = $request->getHeader('Accept');
		$with    = $request->getHeader('X-Requested-With');

		if($with == 'XMLHttpRequest' || (substr($accept, -5) == '+json' || substr($accept, -5) == '/json'))
		{
			$response->setHeader('Content-Type', 'application/json');

			$data = array('success' => false, 'message' => $message);

			if($trace !== null)
			{
				$data['trace'] = $trace;
			}

			$body = json_encode($data);
		}
		else if(strpos($accept, 'text/html') !== false)
		{
			$response->setHeader('Content-Type', 'text/html');

			$body = $this->getErrorTemplate($message, $trace);
		}
		else if(substr($accept, -4) == '+xml' || substr($accept, -4) == '/xml')
		{
			$response->setHeader('Content-Type', 'application/xml');

			$dom     = new DOMDocument();
			$element = $dom->createElement('response');
			$element->appendChild($dom->createElement('success', 'false'));
			$element->appendChild($dom->createElement('message', $message));

			if($trace !== null)
			{
				$element->appendChild($dom->createElement('trace', $trace));
			}

			$dom->appendChild($element);

			$body = $dom->saveXML();
		}
		else
		{
			// sorry we have no idea what content to serve so hopefully 
			// plain text is understandable
			$response->setHeader('Content-Type', 'text/plain');

			$body = $message . "\n" . $trace;
		}

		$response->getBody()->write($body);
	}

	protected function getErrorTemplate($message, $trace)
	{
		// set default values
		$config = $this->config;
		$self   = isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']) ? $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] : $_SERVER['PHP_SELF'];
		$url    = $config['psx_url'] . '/' . $config['psx_dispatch'];
		$base   = parse_url($config['psx_url'], PHP_URL_PATH);
		$render = round(microtime(true) - $GLOBALS['psx_benchmark'], 6);

		// get template
		if(!empty($this->config['psx_error_template']))
		{
			ob_start();

			include $this->config['psx_error_template'];

			$template = ob_get_contents();

			ob_end_clean();
		}
		else
		{
			$template = <<<HTML
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
	{$message}
</p>
<p>
	<pre>{$trace}</pre>
</p>
</body>
</html>
HTML;
		}

		return $template;
	}
}
