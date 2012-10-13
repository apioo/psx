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

/**
 * This class provides an easy way to setup a psx enviroment. The class
 * registers the autoloader and set an error handler.
 *
 * It also sets the include_path of PHP to psx_path_library and from the config.
 * You should keep that in mind if you embed this into another application. Set
 * psx_path_library to an empty value if the library is already in your
 * include_path
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Bootstrap
 * @version    $Revision: 627 $
 */
class PSX_Bootstrap
{
	public function __construct(PSX_Config $config)
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
		if(!empty($config['psx_path_library']))
		{
			$this->addIncludePath($config['psx_path_library']);
		}

		// include core loader
		require_once('PSX/Exception.php');
		require_once('PSX/Loader.php');
		require_once('PSX/Loader/Exception.php');

		// autoload register
		spl_autoload_register('PSX_Bootstrap::autoload');

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
		set_error_handler('PSX_Bootstrap::errorHandler');

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
		set_include_path($path . PATH_SEPARATOR . get_include_path());
	}

	public static function errorHandler($errno, $errstr, $errfile, $errline)
	{
		// if someone add an @ to the function call to supress an error
		// message the error reporting is 0 so in this case we dont
		// throw an exception
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

		if(is_file($fileName))
		{
			require_once($fileName);
		}
	}
}
