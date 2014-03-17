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

namespace PSX\Data\Record\Definition\Reader;

use DOMDocument;
use DOMElement;
use PSX\Data\Record\Definition;
use PSX\Data\Record\Definition\Collection;
use PSX\Data\Record\Definition\Property;
use PSX\Data\Record\Definition\ReaderInterface;

/**
 * XmlString
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class XmlString implements ReaderInterface
{
	public function read($xml)
	{
		$collection = new Collection();

		$dom = new DOMDocument();
		$dom->loadXML($xml);

		// records
		$records = $dom->getElementsByTagName('record');

		for($i = 0; $i < $records->length; $i++)
		{
			$collection->add($this->getDefinition($records->item($i)));
		}

		return $collection;
	}

	protected function getDefinition(DOMElement $element)
	{
		$definition = new Definition($element->getAttribute('name'));
		$properties = $element->getElementsByTagName('property');

		for($i = 0; $i < $properties->length; $i++)
		{
			$definition->addProperty($this->getProperty($properties->item($i)));
		}

		return $definition;
	}

	protected function getProperty(DOMElement $element)
	{
		$name      = $element->getAttribute('name');
		$type      = $element->getAttribute('type');
		$reference = $element->getAttribute('reference');
		$class     = $element->getAttribute('class');
		$required  = $element->getAttribute('required') == 'true';
		$default   = $element->getAttribute('default');
		$title     = $element->getAttribute('title');
		$child     = $element->getElementsByTagName('property')->item(0);

		if($child instanceof DOMElement)
		{
			$child = $this->getProperty($child);
		}

		return new Property($name, $type, $reference, $class, $required, $default, $title, $child);
	}
}
