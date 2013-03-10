<?php
/*
 *  $Id: update-service.php 836 2012-08-26 21:54:07Z k42b3.x@googlemail.com $
 *
 * amun
 * A social content managment system based on the psx framework. For
 * the current version and informations visit <http://amun.phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of amun. amun is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * amun is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with amun. If not, see <http://www.gnu.org/licenses/>.
 */

try
{
	$path  = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : null;
	$level = isset($_SERVER['argv'][2]) ? $_SERVER['argv'][2] : null;

	if(empty($path))
	{
		echo <<<USAGE
NAME
	check-imports.php - checks whether all used classes are imported and have a
	proper namespace declaration

SYNOPSIS
	check-imports.php [LIBRARY_PATH] [LOG_LEVEL]

DESCRIPTION
	This script can be used to see whether your classes follow the PSR-0 
	standard and have a proper namespace declaration.

USAGE;
	}
	else
	{
		// check config
		if(!is_dir($path))
		{
			throw new InvalidArgumentException('Invalid dir ' . $path);
		}

		// config values
		define('PATH', $path);

		// error handler
		set_error_handler('exceptionErrorHandler');

		// set include path
		set_include_path(get_include_path() . PATH_SEPARATOR . $path);

		// register autoloader
		spl_autoload_register('autoloadHandler');

		// logging level
		$level = empty($level) ? Logger::NOTICE : intval($level);

		Logger::setLevel($level);

		// build class index
		$classIndex = buildClassIndex($path);

		// check imports
		organizeImports($path);
	}
}
catch(Exception $e)
{
	echo $e->getMessage();
	exit(1);
}


function organizeImports($path)
{
	$files = scandir($path);

	foreach($files as $file)
	{
		$item = $path . '/' . $file;

		if($file[0] != '.')
		{
			if(is_dir($item))
			{
				organizeImports($item);
			}

			if(is_file($item))
			{
				$info = pathinfo($item);

				if(isset($info['extension']) && $info['extension'] == 'php')
				{
					try
					{
						checkFile($item);
					}
					catch(Exception $e)
					{
						Logger::log(Logger::ERROR, $e->getMessage());
					}
				}
			}
		}
	}
}

function checkFile($file)
{
	global $classIndex;

	$path = substr($file, strlen(PATH) + 1);
	$info = pathinfo($path);

	// parse file
	Logger::log(Logger::INFO, 'Check ' . $path);

	$parser = new TokenParser($file);
	$parser->parse();

	// check namespace
	$hasNs    = $parser->getNamespace();
	$shouldNs = trim($info['dirname'], '/');

	if(empty($hasNs) || $hasNs != str_replace('/', '\\', $shouldNs))
	{
		Logger::log(Logger::ERROR, 'Invalid namespace should be "' . $shouldNs . '" is "' . $hasNs . '" in ' . $path);
	}

	// check class name
	$hasClass    = $parser->getClass();
	$shouldClass = $info['filename'];

	if(empty($hasClass) || $hasClass != $shouldClass)
	{
		Logger::log(Logger::ERROR, 'Class name should be "' . $shouldClass . '" is "' . $hasClass . '" in ' . $path);
	}

	// check whether the classes wich are used are imported through use 
	// statments
	$usedClasses = $parser->getUsedClasses();
	$uses        = $parser->getUses();

	Logger::log(Logger::INFO, 'Found ' . count($usedClasses) . ' used classes');

	foreach($usedClasses as $class)
	{
		Logger::log(Logger::INFO, ' - ' . $class);
	}

	Logger::log(Logger::INFO, 'Found ' . count($uses) . ' namespace imports');

	foreach($uses as $use)
	{
		list($use, $alias) = $use;

		Logger::log(Logger::INFO, ' - ' . $use . ' AS ' . $alias);
	}

	$used = array();

	foreach($usedClasses as $class)
	{
		// if the class name has an underscore fix it to an namespaced name
		if(strpos($class, '_') !== false)
		{
			$class = str_replace('_', '\\', $class);
		}

		// absolute path
		if($class[0] == '\\')
		{
			// we check whether the file exists
			$classParts = explode('\\', substr($class, 1));
			$classFile  = PATH . '/' . implode('/', $classParts) . '.php';

			if(!is_file($classFile) && !(class_exists($class, false) || interface_exists($class, false)))
			{
				Logger::log(Logger::ERROR, 'Class "' . $class . '" does not exist in ' . $path);
			}
			continue;
		}

		// relative path
		$classParts = explode('\\', $class);
		$found      = false;

		foreach($uses as $row)
		{
			list($use, $alias) = $row;

			if($use == $class || $alias == $class)
			{
				$classFile = PATH . '/' . str_replace('\\', '/', $use) . '.php';

				if(!is_file($classFile) && !(class_exists($use, false) || interface_exists($use, false)))
				{
					Logger::log(Logger::ERROR, 'Class "' . $class . '" does not exist in ' . $path);
				}
				else
				{
					$used[] = $use;

					$found = true;
					break;
				}
			}
			else if($alias == $classParts[0])
			{
				$classFile = PATH . '/' . str_replace('\\', '/', $use) . '/' . implode('/', array_slice($classParts, 1)) . '.php';

				if(!is_file($classFile) && !(class_exists($use, false) || interface_exists($use, false)))
				{
					Logger::log(Logger::ERROR, 'Class "' . $class . '" does not exist in ' . $path);
				}
				else
				{
					$used[] = $use;

					$found = true;
					break;
				}
			}
		}

		if(!$found)
		{
			// probably its a class in the same namespace
			$fullClass = $hasNs . '\\' . $class;
			$classFile = PATH . '/' . str_replace('\\', '/', $fullClass) . '.php';

			if(!is_file($classFile) && !(class_exists($use, false) || interface_exists($use, false)))
			{
				Logger::log(Logger::ERROR, 'Class "' . $class . '" does not exist in ' . $path);
			}
			else
			{
				$found = true;
			}
		}

		if(!$found)
		{
			// check whether we have this method in the std library
			if(class_exists($class, false) || interface_exists($class, false))
			{
				$used[] = $class;

				$found = true;
			}
		}

		if(!$found)
		{
			// probably we dont have any use statment for the class we try to  
			// look in our class index
			$className = null;
			$count     = 0;

			foreach($classIndex as $fullClass => $shortClass)
			{
				if($shortClass == $class)
				{
					$className = $fullClass;
					$count++;
				}
			}

			if($count == 0)
			{
				Logger::log(Logger::NOTICE, 'Found no class in index for "' . $class . '" in ' . $path);
			}
			else if($count == 1)
			{
				$used[] = $className;

				Logger::log(Logger::NOTICE, 'Found distinct class in index for "' . $class . '" in ' . $path);
			}
			else
			{
				Logger::log(Logger::NOTICE, 'Found ambiguous classes in index for "' . $class . '" in ' . $path);
			}
		}
	}

	// unique uses
	$used = array_unique($used);

	// remove use wich are in the same namespace
	foreach($used as $k => $use)
	{
		$ns    = substr($use, 0, strlen($hasNs) + 1);
		$class = substr($use, strlen($hasNs) + 1);

		if($ns == $hasNs . '\\' && !empty($class) && strpos($class, '\\') === false)
		{
			$fullClass = $hasNs . '\\' . $class;
			$classFile = PATH . '/' . str_replace('\\', '/', $fullClass) . '.php';

			if(is_file($classFile))
			{
				Logger::log(Logger::NOTICE, 'Found unused import for class ' . $use . ' in ' . $path);

				unset($used[$k]);
			}
		}
	}

	// output
	if(count($used) > 0 && count($used) != count($uses))
	{
		Logger::log(Logger::NOTICE, 'Only the following uses are necessary in ' . $path);

		sort($used);

		foreach($used as $use)
		{
			Logger::log(Logger::NOTICE, 'use ' . $use . ';');
		}

		Logger::log(Logger::NOTICE, '--');
	}
	else if(count($used) == 0 && count($uses) > 0)
	{
		Logger::log(Logger::NOTICE, 'No uses needed in ' . $path);
	}
}

function exceptionErrorHandler($errno, $errstr, $errfile, $errline)
{
	throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

function autoloadHandler($className)
{
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    require $fileName;
}

function buildClassIndex($path)
{
	$result = array();
	$files  = scandir($path);

	foreach($files as $file)
	{
		$item = $path . '/' . $file;

		if($file[0] != '.')
		{
			if(is_dir($item))
			{
				$result = array_merge($result, buildClassIndex($item));
			}

			if(is_file($item))
			{
				$info = pathinfo($item);

				if(isset($info['extension']) && $info['extension'] == 'php')
				{
					$fullClass  = str_replace('/', '\\', trim(substr($info['dirname'], strlen(PATH) + 1) . '/' . $info['filename'], '/'));
					$shortClass = $info['filename'];

					$result[$fullClass] = $shortClass;
				}
			}
		}
	}

	Logger::log(Logger::INFO, 'Found ' . count($result) . ' classes');

	return $result;
}

class Logger
{
	const ERROR  = 0x1;
	const NOTICE = 0x2;
	const INFO   = 0x3;

	private static $_level;

	public static function setLevel($level)
	{
		self::$_level = $level;
	}

	public static function log($level, $msg)
	{
		if($level > self::$_level)
		{
			return;
		}

		switch($level)
		{
			case self::ERROR:
				echo '[ERROR] ' . $msg . PHP_EOL;
				break;

			case self::NOTICE:
				echo '[NOTICE] ' . $msg . PHP_EOL;
				break;

			case self::INFO:
				echo '[INFO] ' . $msg . PHP_EOL;
				break;
		}
	}
}

class TokenParser
{
	private $file;
	private $type;
	private $class;
	private $namespace;

	private $classes = array();
	private $uses = array();

	private $tokens;
	private $i = 0;
	private $len;

	public function __construct($file)
	{
		$this->file = $file;

		if(!is_file($file))
		{
			throw new Exception('Invalid file');
		}
	}

	public function getType()
	{
		return $this->type;
	}

	public function getClass()
	{
		return $this->class;
	}

	public function getNamespace()
	{
		return $this->namespace;
	}

	public function getUsedClasses()
	{
		return $this->classes;
	}

	public function getUses()
	{
		return $this->uses;
	}

	public function getFile()
	{
		return $this->file;
	}

	public function setType($type)
	{
		$this->type = $type;
	}

	public function setClass($class)
	{
		$this->class = $class;
	}

	public function setNamespace($namespace)
	{
		$this->namespace = $namespace;
	}

	public function addClass($class)
	{
		if($class == 'self' || $class == 'parent')
		{
			return;
		}

		$this->classes[] = $class;
	}

	public function addUse($use)
	{
		$this->uses[] = $use;
	}

	public function parse()
	{
		$this->tokens = token_get_all(file_get_contents($this->file));
		$this->len    = count($this->tokens);

		while($this->hasNext())
		{
			$token = $this->next();

			switch($token[0])
			{
				case T_ABSTRACT:
				case T_CLASS:
				case T_INTERFACE:
					if($this->class == null)
					{
						$this->setType($token[0]);

						$token = $this->gotoNextToken(T_STRING);

						$this->setClass($token[1]);
					}
					break;

				case T_CATCH:
					$this->gotoNextToken('(');

					$token = $this->current();

					if($token[0] == T_WHITESPACE)
					{
						$this->next();
					}

					$this->addClass($this->getClassName());
					break;

				case T_DOUBLE_COLON:
					$i = $this->i - 2;
					$classParts = array();

					while($i > 0)
					{
						if($this->tokens[$i][0] == T_STRING)
						{
							$classParts[] = $this->tokens[$i][1];
						}
						else if($this->tokens[$i][0] == T_NS_SEPARATOR)
						{
							$classParts[] = '\\';
						}
						else
						{
							break;
						}

						$i--;
					}

					if(!empty($classParts))
					{
						$class = implode('', array_reverse($classParts));

						$this->addClass($class);
					}
					$this->next();
					break;

				case T_EXTENDS:
				case T_INSTANCEOF:
				//case T_INSTEADOF:
				case T_NEW:
					$this->next();

					$class = $this->getClassName();

					if(!empty($class))
					{
						$this->addClass($class);
					}
					break;

				case T_NAMESPACE:
					$this->next();

					$this->setNamespace($this->getClassName());
					break;

				case T_IMPLEMENTS:
					while($this->hasNext())
					{
						$token = $this->current();

						if($token[0] == T_WHITESPACE || $token[0] == ',')
						{
							$this->next();
							continue;
						}

						$class = $this->getClassName();
						if(!empty($class))
						{
							$this->addClass($class);
						}
						else
						{
							break;
						}
					}
					break;

				case T_USE:
					while($this->hasNext())
					{
						$token = $this->current();

						if($token[0] == T_WHITESPACE || $token[0] == ',')
						{
							$this->next();
							continue;
						}

						$class = $this->getClassName();
						$alias = null;

						if(!empty($class))
						{
							if($this->tokens[$this->i][0] == T_WHITESPACE && $this->tokens[$this->i + 1][0] == T_AS)
							{
								$this->next();
								$this->next();
								$this->next();
								$alias = $this->getClassName();
							}

							if(empty($alias))
							{
								$parts = explode('\\', $class);
								$alias = end($parts);
							}

							$this->addUse(array($class, $alias));
						}
						else
						{
							break;
						}
					}
					break;

				case T_FUNCTION:
					$this->gotoNextToken('(');

					while($this->hasNext())
					{
						$token = $this->current();

						if($token[0] == T_WHITESPACE || $token[0] == ',' || $token[0] == T_VARIABLE)
						{
							$this->next();
							continue;
						}

						$class = $this->getClassName();
						if(!empty($class))
						{
							$this->addClass($class);
						}
						else
						{
							break;
						}
					}
					break;
			}
		}
	}

	protected function hasNext()
	{
		return $this->i < $this->len - 1;
	}

	protected function hasPrev()
	{
		return $this->i > 0;
	}

	protected function next()
	{
		return $this->tokens[$this->i++];
	}

	protected function current()
	{
		return $this->tokens[$this->i];
	}

	protected function prev()
	{
		return $this->tokens[$this->i--];
	}

	protected function gotoNextToken($token, $breakPoint = ';')
	{
		while($this->hasNext())
		{
			$tok = $this->next();

			if($tok[0] == $token)
			{
				return $tok;
				break;
			}
			else if($tok[0] == $breakPoint)
			{
				break;
			}
		}

		return null;
	}

	protected function getClassName()
	{
		$class = '';

		while($this->hasNext())
		{
			$token = $this->next();

			if($token[0] == T_STRING)
			{
				$class.= $token[1];
			}
			else if($token[0] == T_NS_SEPARATOR)
			{
				$class.= '\\';
			}
			else
			{
				$this->prev();
				break;
			}
		}

		return $class;
	}

	public function gotoClassName()
	{
		while($this->hasNext())
		{
			$token = $this->next();

			if($token[0] == T_STRING || $token[0] == T_NS_SEPARATOR)
			{
				$this->prev();

				return $token;
			}
		}

		return null;
	}
}
