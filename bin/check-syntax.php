<?php

if(!isset($_SERVER['argv'][1]) || !is_dir($_SERVER['argv'][1]))
{
	throw new Exception('Argument must be a path to psx');
}


define('PATH', $_SERVER['argv'][1]);
define('DEBUG', isset($_SERVER['argv'][2]));


require_once(PATH . '/library/PSX/Config.php');
require_once(PATH . '/library/PSX/Bootstrap.php');

$config = new PSX_Config(PATH . '/configuration.php');
$config['psx_path_cache']    = PATH . '/cache';
$config['psx_path_library']  = PATH . '/library';
$config['psx_path_module']   = PATH . '/module';
$config['psx_path_template'] = PATH . '/template';

$bootstrap = new PSX_Bootstrap($config);

// classes wich should be excluded from the class check
$exclude = array('Memcache');

try
{
	checkSyntax(PSX_PATH_LIBRARY);

	echo 'OK';
}
catch(Exception $e)
{
	// most errors should be fatal errors but in case we have an exception
	echo $e->getMessage();
}

function checkSyntax($path)
{
	$count = 0;
	$files = scandir($path);

	foreach($files as $file)
	{
		$item = $path . '/' . $file;

		if($file[0] != '.')
		{
			if(is_dir($item))
			{
				$count+= checkSyntax($item);
			}

			if(is_file($item))
			{
				$info = pathinfo($item);

				if($info['extension'] == 'php')
				{
					// require file for syntax check
					require_once($item);

					// check used classes
					$classes = getUsedClasses($item);

					foreach($classes as $class)
					{
						$class = new ReflectionClass($class);
					}

					$count++;
				}
			}
		}
	}

	return $count;
}

function getUsedClasses($file, array $loaded = array())
{
	$result  = array();
	$classes = array();

	if(!is_file($file))
	{
		throw new Exception('Is not a file: ' . $file);
	}

	if(DEBUG)
	{
		echo 'Check: ' . $file . "\n";
	}

	$source = file_get_contents($file);
	$tokens = token_get_all($source);
	$count  = count($tokens);

	for($i = 2; $i < $count; $i++)
	{
		// class definition
		if($tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING)
		{
			$classes[] = $tokens[$i][1];
		}
		// type hinting class in method
		else if($tokens[$i - 2][0] == T_STRING && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_VARIABLE)
		{
			$classes[] = $tokens[$i - 2][1];
		}
		// exceptions or class calls
		else if($tokens[$i - 2][0] == T_NEW && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING)
		{
			if(!in_array($tokens[$i][1], array('self')))
			{
				$classes[] = $tokens[$i][1];
			}
		}
		// extends
		else if($tokens[$i - 2][0] == T_EXTENDS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING)
		{
			$classes[] = $tokens[$i][1];
		}
		// implements
		else if($tokens[$i - 2][0] == T_IMPLEMENTS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING)
		{
			$classes[] = $tokens[$i][1];
		}
		// static calls
		else if($tokens[$i - 1][0] == T_STRING && $tokens[$i][0] == T_DOUBLE_COLON)
		{
			if(!in_array($tokens[$i - 1][1], array('self', 'parent')))
			{
				$classes[] = $tokens[$i - 1][1];
			}
		}
	}

	$classes = array_unique($classes);
	$files   = array();

	// remove exclude classes
	foreach($classes as $key => $class)
	{
		if(in_array($class, $GLOBALS['exclude']))
		{
			unset($classes[$key]);
		}
	}

	// in case we load any new classes we must run the parser through all the
	// new classes
	foreach($classes as $class)
	{
		if(!in_array($class, $loaded))
		{
			$file  = PSX_PATH_LIBRARY . '/' . str_replace('_', '/', $class) . '.php';
			$class = new ReflectionClass($class);

			if($class->isUserDefined())
			{
				$classes = array_merge($classes, getUsedClasses($file, array_merge($loaded, $classes)));
			}
		}
	}

	return $classes;
}


