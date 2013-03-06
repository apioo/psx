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

/**
 * This script opens each file in the given directory and tries to import all
 * used classes in the script. This script can be dangerous because it 
 * overwrites all content between the page and class comment. Use this only on
 * files wich have the standard format
 *
 * > php organize-imports.php
 */
try
{
	// config values
	define('PSX_PATH', 'test');

	// check config
	if(!is_dir(PSX_PATH))
	{
		throw new InvalidArgumentException('Invalid dir ' . PSX_PATH);
	}

	Logger::setLevel(Logger::NOTICE);

	set_error_handler("exceptionErrorHandler");

	// update service
	organizeImports(PSX_PATH);
}
catch(Exception $e)
{
	echo $e->getMessage() . "\n";
	echo $e->getTraceAsString();
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
					checkFile($item);
				}
			}
		}
	}
}

function checkFile($file)
{
	try
	{
		$parts  = explode('/', $file);
		$info   = pathinfo(substr($file, strlen(PSX_PATH)));
		$vendor = $parts[0];

		// parse file
		Logger::log(Logger::INFO, 'Check ' . $file);

		$parser = new TokenParser($file);
		$parser->parse();

		// check namespace
		$hasNs    = $parser->getNamespace();
		$shouldNs = trim($info['dirname'], '/');

		if(empty($hasNs) || $hasNs != str_replace('/', '\\', $shouldNs))
		{
			Logger::log(Logger::NOTICE, 'Invalid namespace should be "' . $shouldNs . '" is "' . $hasNs . '" in ' . $file);
		}

		// check whether the classes wich are used have are important through
		// use statments
		$usedClasses = $parser->getUsedClasses();
		$uses        = $parser->getUses();

		Logger::log(Logger::INFO, 'Found ' . count($usedClasses) . ' used classes');
		Logger::log(Logger::INFO, 'Found ' . count($uses) . ' namespace imports');

		foreach($usedClasses as $class)
		{
			// check whether we have this method in the std library
			if(class_exists($class, false) || interface_exists($class, false))
			{
				continue;
			}

			// absolute path
			if($class[0] == '\\')
			{
				// we check whether the file exists if the file refers to the 
				// same vendor
				$classParts = explode('\\', substr($class, 1));

				if($classParts[0] == $vendor)
				{
					$classFile = PSX_PATH . '/' . implode('/', $classParts) . '.php';

					if(!is_file($classFile))
					{
						Logger::log(Logger::NOTICE, 'Class "' . $class . '" does not exist in ' . $file);
					}
					continue;
				}
			}

			// relative path
			$classParts = explode('\\', $class);
			$found = false;

			foreach($uses as $row)
			{
				list($use, $alias) = $row;

				$useParts = explode('\\', $use);

				// if the alias refers to an known class or interface
				if($alias == $class)
				{
					if(class_exists($use, false) || interface_exists($use, false))
					{
						$found = true;
						break;
					}
				}

				if($use == $class || $alias == $class || end($useParts) == $class)
				{
					$classFile = PSX_PATH . '/' . str_replace('\\', '/', $use) . '.php';

					if(!is_file($classFile))
					{
						Logger::log(Logger::NOTICE, 'Class "' . $class . '" does not exist in ' . $file);
					}
					else
					{
						$found = true;
						break;
					}
				}
				else if(end($useParts) == $classParts[0] || $alias == $classParts[0])
				{
					$classFile = PSX_PATH . '/' . str_replace('\\', '/', $use) . '/' . implode('/', array_slice($classParts, 1)) . '.php';

					if(!is_file($classFile))
					{
						Logger::log(Logger::NOTICE, 'Class "' . $class . '" does not exist in ' . $file);
					}
					else
					{
						$found = true;
						break;
					}
				}
			}

			if(!$found)
			{
				// probably its a class in the same namespace
				$fullClass = $hasNs . '\\' . $class;
				$classFile = PSX_PATH . '/' . str_replace('\\', '/', $fullClass) . '.php';

				if(!is_file($classFile))
				{
					Logger::log(Logger::NOTICE, 'Class "' . $class . '" does not exist in ' . $file);
				}
			}
		}
	}
	catch(Exception $e)
	{
		Logger::log(Logger::ERROR, $e->getMessage());
	}
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
			case self::INFO:
				echo '[INFO] ' . $msg . PHP_EOL;
				break;

			case self::NOTICE:
				echo '[NOTICE] ' . $msg . PHP_EOL;
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
					$this->setType($token[0]);

					$token = $this->gotoNextToken(T_STRING);

					$this->setClass($token[1]);
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

				/*
				case T_DOUBLE_COLON:
					$this->prev();

					$this->classes[] = $this->getClassNamePrev();
					break;
				*/

				case T_EXTENDS:
				case T_INSTANCEOF:
				//case T_INSTEADOF:
				case T_NEW:
					$this->next();

					$this->addClass($this->getClassName());
					break;

				case T_NAMESPACE:
					$this->next();

					$this->setNamespace($this->getClassName());
					break;

				case T_IMPLEMENTS:
					while(true)
					{
						$token = $this->current();

						if($token[0] == T_WHITESPACE)
						{
							$this->next();
							continue;
						}
						else if($token[0] == ',')
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
					$this->next();

					$class = $this->getClassName();
					$alias = null;
					$token = $this->next();

					if($token[0] == T_AS)
					{
						$this->next();
						$alias = $this->getClassName();
					}

					$this->addUse(array($class, $alias));
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

	protected function gotoNextToken($token)
	{
		while($this->hasNext())
		{
			$tok = $this->next();

			if($tok[0] == $token)
			{
				return $tok;
				break;
			}
		}

		return null;
	}

	protected function gotoPrevToken($token)
	{
		while($this->hasPrev())
		{
			$tok = $this->prev();

			if($tok[0] == $token)
			{
				return $tok;
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

	protected function getClassNamePrev()
	{
		$class = '';

		while($this->hasPrev())
		{
			$token = $this->prev();

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
				break;
			}
		}

		return $class;
	}
}

// exception error handler
function exceptionErrorHandler($errno, $errstr, $errfile, $errline)
{
	throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
