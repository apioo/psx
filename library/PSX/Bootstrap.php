<?php
/*
 *  $Id: Bootstrap.php 627 2012-08-25 11:19:49Z k42b3.x@googlemail.com $
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

namespace PSX;

use ErrorException;

/**
 * This class provides an easy way to setup a psx enviroment. If psx_autoload is
 * true an PSR-0 autoloader is registered . If psx_include_path is true the
 * library folder is added to the include path
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Bootstrap
 * @version    $Revision: 627 $
 */
class Bootstrap
{
	public function __construct(Config $config)
	{
		// define benchmark
		$GLOBALS['psx_benchmark'] = microtime(true);

		// setting default headers
		header('Content-type: text/html; charset=UTF-8');
		header('X-Powered-By: psx');
		header('Expires: Thu, 09 Oct 1986 01:00:00 GMT');
		header('Last-Modified: Thu, 09 Oct 1986 01:00:00 GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Pragma: no-cache');

		// define paths
		define('PSX_PATH_CACHE', $config['psx_path_cache']);
		define('PSX_PATH_LIBRARY', $config['psx_path_library']);
		define('PSX_PATH_MODULE', $config['psx_path_module']);
		define('PSX_PATH_TEMPLATE', $config['psx_path_template']);

		// set include path
		if($config['psx_include_path'] === true)
		{
			$this->addIncludePath($config['psx_path_library']);
		}

		// if enabled handle autoloading
		if($config['psx_autoload'] === true)
		{
			// include core loader
			require_once('PSX' . DIRECTORY_SEPARATOR . 'Exception.php');
			require_once('PSX' . DIRECTORY_SEPARATOR . 'Loader.php');

			// autoload register
			spl_autoload_register('\PSX\Bootstrap::autoload');
		}

		// error handling
		if($config['psx_debug'] === true)
		{
			$errorReporting = E_ALL | E_STRICT;
		}
		else
		{
			$errorReporting = 0;
		}

		error_reporting($errorReporting);
		set_error_handler('\PSX\Bootstrap::errorHandler');

		// ini settings
		ini_set('date.timezone', $config['psx_timezone']);
		ini_set('session.use_only_cookies', '1');
		ini_set('docref_root', '');
		ini_set('html_errors', '0');

		// define in psx
		define('PSX', true);
	}

	public function addIncludePath($path)
	{
		if(strpos(get_include_path(), $path) === false)
		{
			set_include_path($path . PATH_SEPARATOR . get_include_path());
		}
	}

	public static function errorHandler($errno, $errstr, $errfile, $errline)
	{
		// if someone add an @ to the function call to supress an error message
		// the error reporting is 0 so in this case we dont throw an exception
		if(error_reporting() == 0)
		{
			return false;
		}
		else
		{
			throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
		}
	}

	/**
	 * Implementation of the standard autoload function.
	 *
	 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
	 * @return void
	 */
	public static function autoload($className)
	{
		$className = ltrim($className, '\\');
		$fileName  = '';
		$namespace = '';

		if($lastNsPos = strripos($className, '\\'))
		{
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}

		$fileName.= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		$filePath = stream_resolve_include_path($fileName);

		if($filePath !== false)
		{
			require($filePath);
		}
	}
}
