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

if(!isset($_SERVER['argv'][1]))
{
	throw new Exception('Argument one must be a version number');
}


define('VERSION', $_SERVER['argv'][1]);
define('PACKAGE', 'package.xml');


date_default_timezone_set('Europe/Berlin');


$impl = new DOMImplementation();

$doc = $impl->createDocument(NULL, 'package');
$doc->formatOutput = true;
$doc->preserveWhiteSpace = false;
$doc->loadXML(file_get_contents(PACKAGE));


// update meta
updateMeta($doc);


// add content
addContent($doc);


// add changelog
addChangelog($doc);


// refresh changelog
refreshChangelog($doc);


// save package
file_put_contents(PACKAGE, $doc->saveXML());


/**
 * Update all meta informations for the current release
 */
function updateMeta(DomDocument $doc)
{
	// update date / version
	$elements = getChildElements($doc->documentElement);

	foreach($elements as $element)
	{
		switch($element->nodeName)
		{
			case 'date':

				$element->nodeValue = date('Y-m-d');

				break;

			case 'version':

				$childElements = getChildElements($element);

				foreach($childElements as $childElement)
				{
					$childElement->nodeValue = VERSION;
				}

				break;

			case 'notes':

				$element->nodeValue = getChangelog();

				break;
		}
	}
}

/**
 * Add all files from the library to the contents node
 */
function addContent(DomDocument $doc)
{
	// remove all contents
	$contents = $doc->getElementsByTagName('contents')->item(0);

	if(!$contents instanceof DomElement)
	{
		throw new Exception('Could not found contents element');
	}

	while($contents->firstChild)
	{
		$contents->removeChild($contents->firstChild);
	}

	// add new contents
	$root = $doc->createElement('dir');
	$root->setAttribute('name', '/');

	generatePearContent('psx/library', $root, 'php');

	$tests = $doc->createElement('dir');
	$tests->setAttribute('name', 'tests');

	generatePearContent('psx/tests', $tests, 'test');

	$root->appendChild($tests);

	$contents->appendChild($root);
}

function generatePearContent($path, DomElement $node, $role)
{
	global $doc;

	$files = scandir($path);

	foreach($files as $f)
	{
		if($f[0] != '.' && $f[0] != '_')
		{
			$item = $path . '/' . $f;

			if(is_dir($item))
			{
				$dir = $doc->createElement('dir');
				$dir->setAttribute('name', $f);

				generatePearContent($item, $dir, $role);

				$node->appendChild($dir);
			}

			if(is_file($item))
			{
				$file = $doc->createElement('file');
				$file->setAttribute('name', $f);
				$file->setAttribute('role', $role);

				$node->appendChild($file);
			}
		}
	}
}

/**
 * Adds a changelog entry if the current version is not available in the
 * release nodes
 */
function addChangelog(DomDocument $doc)
{
	$changelog = $doc->getElementsByTagName('changelog')->item(0);

	if(!$changelog instanceof DomElement)
	{
		throw new Exception('Could not found changelog element');
	}

	$releases = getChildElements($changelog);
	$found    = false;

	foreach($releases as $release)
	{
		$version = getChildElements(getChildElements($release, 'version'), 'release');

		if($version->nodeValue == VERSION)
		{
			$found = true;
		}
	}

	if(!$found)
	{
		$release = $doc->createElement('release');

		$version = $doc->createElement('version');
		$version->appendChild($doc->createElement('release', VERSION));
		$version->appendChild($doc->createElement('api', VERSION));
		$release->appendChild($version);

		$stability = $doc->createElement('stability');
		$stability->appendChild($doc->createElement('release', 'beta'));
		$stability->appendChild($doc->createElement('api', 'beta'));
		$release->appendChild($stability);

		$release->appendChild($doc->createElement('date', date('Y-m-d')));

		$license = $doc->createElement('license', 'GPLv3 License');
		$license->setAttribute('uri', 'http://www.gnu.org/licenses/');
		$release->appendChild($license);

		$release->appendChild($doc->createElement('notes', getChangelog()));

		$changelog->appendChild($release);
	}
}

/**
 * Update the changelog according to the changelog.txt in case something has
 * changed
 */
function refreshChangelog(DomDocument $doc)
{
	$changelog = $doc->getElementsByTagName('changelog')->item(0);

	if(!$changelog instanceof DomElement)
	{
		throw new Exception('Could not found changelog element');
	}

	$releases = getChildElements($changelog);
	$found    = false;

	foreach($releases as $release)
	{
		$version = getChildElements(getChildElements($release, 'version'), 'release');

		if($version instanceof DomElement)
		{
			$log = getChangelog($version->nodeValue);

			if(!empty($log))
			{
				$note = getChildElements($release, 'notes');

				if($note instanceof DomElement)
				{
					$note->nodeValue = $log;
				}
			}
		}
	}
}

/**
 * Returns the changelog as string for the specified $version. If $version is
 * null the current version is assumed
 *
 * @return string
 */
function getChangelog($version = null)
{
	$version = $version == null ? VERSION : $version;
	$log     = '';
	$content = file_get_contents('../changelog.txt');
	$content = explode("\n", $content);
	$found   = false;

	foreach($content as $line)
	{
		$line = trim($line);

		if(isset($line[0]) && is_numeric($line[0]))
		{
			if($line == $version)
			{
				$found = true;
			}
			else if($found)
			{
				break;
			}
		}
		else
		{
			if($found && !empty($line))
			{
				$log.= $line . "\n";
			}
		}
	}

	return trim($log);
}

function getChildElements(DomElement $element, $nodeName = null)
{
	$elements = array();
	$len      = $element->childNodes->length;

	for($i = 0; $i < $len; $i++)
	{
		$child = $element->childNodes->item($i);

		if($child instanceof DomElement)
		{
			if($nodeName !== null)
			{
				if($child->nodeName == $nodeName)
				{
					return $child;
				}
			}
			else
			{
				$elements[] = $child;
			}
		}
	}

	return $elements;
}

