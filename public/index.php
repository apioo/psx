<?php
/*
 *  $Id: index.php 646 2012-09-30 23:00:35Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

require_once('../library/PSX/Config.php');
require_once('../library/PSX/Bootstrap.php');

$config    = new PSX_Config('../configuration.php');
$bootstrap = new PSX_Bootstrap($config);

ob_start('responseProcess');

try
{
	// initialize base class
	$base = PSX_Base_Default::initInstance($config);

	// load module
	$module = loadModule($base);

	// get output
	$content = ob_get_contents();

	// proccess response
	$response = $module->processResponse($content);
}
catch(Exception $e)
{
	$code    = isset(PSX_Http::$codes[$e->getCode()]) ? $e->getCode() : 500;
	$accept  = PSX_Base::getRequestHeader('Accept');
	$message = $e->getMessage();
	$trace   = '';

	if($config['psx_debug'] === true)
	{
		$message.= ' in ' . $e->getFile() . ' on line ' . $e->getLine();
		$trace   = $e->getTraceAsString();
	}

	// build response
	if(strpos($accept, 'text/html') !== false)
	{
		PSX_Base::setResponseCode(200);
		header('Content-type: text/html');

		$response = <<<HTML
<html>
<head>
	<title>Exception</title>
</head>
<body>
	<h1>Internal Server Error</h1>
	<p>{$message}</p>
	<p><pre>{$trace}</pre></p>
</body>
</html>
HTML;
	}
	else
	{
		PSX_Base::setResponseCode($code);
		header('Content-type: text/plain');

		$response = $message . "\n" . $trace;
	}

	// logging
	PSX_Log::error($e->getMessage() . "\n" . 'Stack trace:' . "\n" . $e->getTraceAsString() . "\n");
}

ob_end_clean();

echo $response;

/**
 * responseProcess
 *
 * Callback function wich is called by the ob_start() function. This function
 * handles errors wich are not cought by the ErrorException handler.
 *
 * @return string
 */
function responseProcess($content)
{
	$lastError = error_get_last();

	if($lastError)
	{
		return $lastError['message'] . ' in ' . $lastError['file'] . ' on line ' . $lastError['line'] . "\n";
	}

	return $content;
}

/**
 * loadModule
 *
 * Loads the requested module depending on the psx_module_input field from the
 * config
 *
 * @return PSX_ModuleAbstract
 */
function loadModule(PSX_Base $base)
{
	$config  = $base->getConfig();
	$default = $config['psx_module_default'];
	$input   = $config['psx_module_input'];
	$length  = $config['psx_module_input_length'];

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
		throw new PSX_Exception('Invalid signs in input');
	}

	if($length != 0)
	{
		if(strlen($x) > $length)
		{
			throw new PSX_Exception('Max length of input is ' . $length, 414);
		}
	}

	return $base->getLoader()->load($x);
}


