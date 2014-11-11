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

namespace PSX;

use Doctrine\Common\Annotations\AnnotationRegistry;
use ErrorException;

/**
 * This class provides an easy way to setup a psx enviroment. If psx_autoload is
 * true an PSR-0 autoloader is registered . If psx_include_path is true the
 * library folder is added to the include path
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Bootstrap
{
	public static function setupEnvironment(Config $config)
	{
		if(!defined('PSX'))
		{
			// define benchmark
			$GLOBALS['psx_benchmark'] = microtime(true);

			// define paths
			define('PSX_PATH_CACHE', $config['psx_path_cache']);
			define('PSX_PATH_LIBRARY', $config['psx_path_library']);

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

			// annotation autoload
			$namespaces = $config->get('annotation_autoload');
			if(!empty($namespaces) && is_array($namespaces))
			{
				self::registerAnnotationLoader($namespaces);
			}

			// ini settings
			ini_set('date.timezone', $config['psx_timezone']);
			ini_set('session.use_only_cookies', '1');
			ini_set('docref_root', '');
			ini_set('html_errors', '0');

			// define in psx
			define('PSX', true);
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

	protected static function registerAnnotationLoader(array $namespaces)
	{
		AnnotationRegistry::reset();
		AnnotationRegistry::registerLoader(function($class) use ($namespaces){

			foreach($namespaces as $namespace)
			{
				if(strpos($class, $namespace) === 0)
				{
					spl_autoload_call($class);

					return class_exists($class, false);
				}
			}

		});
	}
}
