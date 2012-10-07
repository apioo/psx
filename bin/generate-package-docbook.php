<?php

require_once('../library/PSX/Config.php');
require_once('../library/PSX/Bootstrap.php');

$config    = new PSX_Config('../configuration.php');
$bootstrap = new PSX_Bootstrap($config);

parsePackages('../doc/docbook/packages');


function parsePackages($path)
{
	$files = scandir($path);

	foreach($files as $file)
	{
		$item = $path . '/' . $file;

		if($file[0] != '.' && is_file($item))
		{
			$info = pathinfo($item);

			if($info['extension'] == 'xml')
			{
				try
				{
					$packageName = ucfirst(strtolower($info['filename']));

					buildPackage($item, $packageName);
				}
				catch(Exception $e)
				{
					echo $e->getMessage() . "\n" . $e->getTraceAsString();
				}
			}
		}
	}
}

function buildPackage($file, $name)
{
	// load xml
	$impl = new DOMImplementation();

	$doc = $impl->createDocument(NULL, 'package');
	$doc->formatOutput = true;
	$doc->preserveWhiteSpace = false;
	$doc->loadXML(file_get_contents($file));

	// load package classes
	$classes  = array();
	$result   = array();
	$basePath = '../library/';

	$classes = array_merge($classes, scanRecClasses($basePath . 'PSX/' . $name, $basePath));
	$classes = array_merge($classes, scanRecClasses($basePath . 'PSX/' . $name . '.php', $basePath));

	sort($classes);

	foreach($classes as $className)
	{
		$class = ClassUsed::factory($className, 0);

		getClassDependencies($class);

		$result[] = $class;
	}

	// write dependecies
	//writeDependecies($doc, $name, $result);

	// write synopsis
	writeSynopsis($doc, $name, $result);

	// save package
	file_put_contents($file, $doc->saveXML());
}

function scanRecClasses($path, $basePath)
{
	$classes = array();

	if(is_dir($path))
	{
		$files = scandir($path);

		foreach($files as $file)
		{
			$item = $path . '/' . $file;

			if($file[0] != '.')
			{
				$classes = array_merge($classes, scanRecClasses($item, $basePath));
			}
		}
	}

	if(is_file($path))
	{
		$info = pathinfo($path);

		if($info['extension'] == 'php')
		{
			$name = substr(str_replace('/', '_', substr($path, strlen($basePath))), 0, -4);

			$classes[] = $name;
		}
	}

	return $classes;
}

function getDeclardeClasses($prefix = null)
{
	$defined = array_merge(get_declared_classes(), get_declared_interfaces());
	$classes = array();

	foreach($defined as $class)
	{
		if($prefix === null || substr($class, 0, strlen($prefix)) == $prefix)
		{
			$classes[] = $class;
		}
	}

	sort($classes);

	return $classes;
}

function getClassDependencies(ClassUsed $parentClass, $deep = 0, array $loaded = array())
{
	$classes = array();
	$line    = 1;

	$source  = file_get_contents($parentClass->getFile());
	$tokens  = token_get_all($source);
	$count   = count($tokens);

	for($i = 2; $i < $count; $i++)
	{
		$class = null;

		// class definition
		if($tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING)
		{
			$class = $tokens[$i][1];
		}
		// type hinting class in method
		else if($tokens[$i - 2][0] == T_STRING && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_VARIABLE)
		{
			$class = $tokens[$i - 2][1];
		}
		// exceptions or class calls
		else if($tokens[$i - 2][0] == T_NEW && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING)
		{
			if($tokens[$i][1] != 'self')
			{
				$class = $tokens[$i][1];
			}
		}
		// extends
		else if($tokens[$i - 2][0] == T_EXTENDS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING)
		{
			$class = $tokens[$i][1];
		}
		// implements
		else if($tokens[$i - 2][0] == T_IMPLEMENTS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING)
		{
			$class = $tokens[$i][1];
		}
		// static calls
		else if($tokens[$i - 1][0] == T_STRING && $tokens[$i][0] == T_DOUBLE_COLON)
		{
			$class = $tokens[$i - 1][1];
		}

		if($class !== null && !in_array($class, $loaded))
		{
			$classes[] = ClassUsed::factory($class, $line);
		}

		if($tokens[$i][0] == T_WHITESPACE && strpos($tokens[$i][1], "\n") !== false)
		{
			$line++;
		}
	}

	$classes = array_unique($classes);

	sort($classes, SORT_STRING);

	foreach($classes as $class)
	{
		if($class instanceof ClassUsed)
		{
			if(!$parentClass->hasClass($class->getName()))
			{
				$parentClass->addDependency($class);

				getClassDependencies($class, $deep + 1, array_merge($classes, $loaded));
			}
		}
	}
}

function writeSynopsis($doc, $name, array $classes)
{
	// find chapter
	$chapterId = strtolower($name) . '.synopsis';
	$chapter   = $doc->getElementById($chapterId);

	if($chapter == null)
	{
		throw new Exception('Found no chapter id ' . $chapterId);
	}

	// remove existing nodes
	while($chapter->firstChild)
	{
		$chapter->removeChild($chapter->firstChild);
	}

	// add title
	$chapter->appendChild($doc->createElement('title', 'Reference (' . count($classes) . ')'));

	// write class synopsis
	foreach($classes as $class)
	{
		writeClass($doc, $class->getClass(), $chapter);
	}
}

function writeClass(DomDocument $doc, ReflectionClass $class, DomElement $chapter)
{
	// add element
	$section = $doc->createElement('sect1');
	$section->appendChild($doc->createElement('title', $class->getName()));

	$comment = $doc->createDocumentFragment();
	$comment->appendXML(parseDocComment($class->getDocComment()));

	// read annotations
	$para = $doc->createElement('para');
	$para->appendChild($comment);

	$section->appendChild($para);

	$classsynopsis = $doc->createElement('classsynopsis');

	// class name
	$ooclass = $doc->createElement('ooclass');
	$ooclass->appendChild($doc->createElement('classname', $class->getName()));

	$classsynopsis->appendChild($ooclass);

	// parent class
	$parentClass = $class->getParentClass();

	if($parentClass != null)
	{
		$parentOoclass = $doc->createElement('ooclass');
		$parentOoclass->appendChild($doc->createElement('classname', $parentClass->getName()));

		$classsynopsis->appendChild($parentOoclass);
	}

	// implements
	$interfaces = get_declared_interfaces();

	foreach($interfaces as $interface)
	{
		if($class->getName() != $interface && $class->implementsInterface($interface))
		{
			$oointerface = $doc->createElement('oointerface');
			$oointerface->appendChild($doc->createElement('interfacename', $interface));

			$classsynopsis->appendChild($oointerface);
		}
	}

	// throws
	// @todo check in doc wich exception is thrown
	if(false)
	{
		$ooexception = $doc->createElement('ooexception');
		$ooexception->appendChild($doc->createElement('exceptionname', $class->getName()));

		$classsynopsis->appendChild($ooexception);
	}

	// constants
	$constants = $class->getConstants();

	foreach($constants as $key => $value)
	{
		$fieldsynopsis = $doc->createElement('fieldsynopsis');

		$type = is_int($value) ? 'integer' : 'string';

		$fieldsynopsis->appendChild($doc->createElement('modifier', 'const'));
		$fieldsynopsis->appendChild($doc->createElement('type', $type));
		$fieldsynopsis->appendChild($doc->createElement('varname', $key));
		$fieldsynopsis->appendChild($doc->createElement('initializer', $value));

		$classsynopsis->appendChild($fieldsynopsis);
	}

	// properties
	$properties = $class->getProperties();

	foreach($properties as $property)
	{
		if(!$property->isPublic())
		{
			continue;
		}

		$fieldsynopsis = $doc->createElement('fieldsynopsis');

		// add modifiers
		$modifiers = Reflection::getModifierNames($property->getModifiers());

		foreach($modifiers as $modifier)
		{
			$fieldsynopsis->appendChild($doc->createElement('modifier', $modifier));
		}

		//$fieldsynopsis->appendChild($doc->createElement('type', $type));
		$fieldsynopsis->appendChild($doc->createElement('varname', $property->getName()));

		$classsynopsis->appendChild($fieldsynopsis);
	}

	// methods
	$methods = $class->getMethods();

	foreach($methods as $method)
	{
		if(!$method->isPublic())
		{
			continue;
		}

		// add method docblock
		/*
		$comment = parseDocComment($method->getDocComment());

		if(!empty($comment))
		{
			$methodcomment = $doc->createElement('classsynopsisinfo', $comment);
			$methodcomment->setAttribute('role', 'comment');

			$classsynopsis->appendChild($methodcomment);
		}
		*/

		// parse annotations
		$annotations = parseAnnotations($method->getDocComment());

		$methodsynopsis = $doc->createElement('methodsynopsis');

		// add modifiers
		$modifiers = Reflection::getModifierNames($method->getModifiers());

		foreach($modifiers as $modifier)
		{
			$methodsynopsis->appendChild($doc->createElement('modifier', $modifier));
		}

		// add return type
		if(isset($annotations['return'][0]))
		{
			$type = $annotations['return'][0];

			$methodsynopsis->appendChild($doc->createElement('type', $type));
		}

		// add method name
		$methodsynopsis->appendChild($doc->createElement('methodname', $method->getName()));

		// add parameters
		$parameters = $method->getParameters();

		if(!empty($parameters))
		{
			foreach($parameters as $k => $parameter)
			{
				$methodparam = $doc->createElement('methodparam');
				$methodparam->appendChild($doc->createElement('type', isset($annotations['param'][$k]) ? $annotations['param'][$k] : 'mixed'));
				$methodparam->appendChild($doc->createElement('parameter', $parameter->getName()));

				if($parameter->isOptional())
				{
					$methodparam->setAttribute('choice', 'opt');
				}

				$methodsynopsis->appendChild($methodparam);
			}
		}
		else
		{
			$methodsynopsis->appendChild($doc->createElement('void'));
		}

		$classsynopsis->appendChild($methodsynopsis);
	}

	$section->appendChild($classsynopsis);
	$chapter->appendChild($section);
}

function parseDocComment($doc)
{
	$lines = explode("\n", $doc);
	$text  = '';

	foreach($lines as $line)
	{
		$line = trim($line);
		$line = substr($line, 2);

		if(trim($line) == '*')
		{
		}
		else if($line[0] == '@')
		{
		}
		else
		{
			$text.= $line . "\n";
		}
	}

	return trim($text);
}

function parseAnnotations($doc)
{
	$annotations = array();
	$lines = explode("\n", $doc);
	$text  = '';

	foreach($lines as $line)
	{
		$line = trim($line);
		$line = substr($line, 2);

		if($line[0] == '@')
		{
			$line  = substr($line, 1);
			$pos   = strpos($line, ' ');

			if($pos !== false)
			{
				$key   = substr($line, 0, $pos);
				$value = substr($line, $pos);
			}
			else
			{
				$key   = $line;
				$value = null;
			}

			$key   = trim($key);
			$value = trim($value);

			if($key == 'param')
			{
				$value = explode(' ', $value);
				$value = $value[0];
			}

			if(!empty($key))
			{
				if(!isset($annotations[$key]))
				{
					$annotations[$key] = array();
				}

				$annotations[$key][] = $value;
			}
		}
	}

	return $annotations;
}

function searchRecClass($name, array $classes)
{
	foreach($classes as $class)
	{
		if($name == $class->getName())
		{
			return true;
		}

		if(searchRecClass($name, $class->getDependecies()))
		{
			return true;
		}
	}

	return false;
}

class ClassUsed
{
	private $name;
	private $line;

	private $parent;
	private $dependencies = array();

	public function __construct($name, $line)
	{
		$this->name = $name;
		$this->line = $line;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getLine()
	{
		return $this->line;
	}

	public function getParent()
	{
		return $this->parent;
	}

	public function getFile()
	{
		return PSX_PATH_LIBRARY . '/' . str_replace('_', '/', $this->name) . '.php';
	}

	public function getClass()
	{
		return new ReflectionClass($this->name);
	}

	public function setParent(ClassUsed $parent)
	{
		$this->parent = $parent;
	}

	public function hasClass($name)
	{
		if($this->name == $name)
		{
			return true;
		}

		if($this->parent === null)
		{
			return false;
		}

		if($this->parent->getName() == $name)
		{
			return true;
		}

		if($this->parent->hasClass($name))
		{
			return true;
		}

		return false;
	}

	public function addDependency(ClassUsed $class)
	{
		$class->setParent($this);

		$this->dependencies[] = $class;
	}

	public function getDependencies()
	{
		return $this->dependencies;
	}

	public function __toString()
	{
		return $this->name;
	}

	public static function factory($name, $line)
	{
		try
		{
			$class = new ClassUsed($name, $line);

			if(!is_file($class->getFile()))
			{
				throw new Exception('File does not exist');
			}

			if(!$class->getClass()->isUserDefined())
			{
				throw new Exception('Invalid class');
			}

			return $class;
		}
		catch(Exception $e)
		{
			return null;
		}
	}
}



