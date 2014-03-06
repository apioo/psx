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

namespace PSX\Hostmeta;

use SimpleXMLElement;
use DateTime;
use XMLWriter;

/**
 * Class wich represents an Extensible Resource Descriptor. It offers some
 * methods to easily access the values.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://docs.oasis-open.org/xri/xrd/v1.0/xrd-1.0.html
 */
class Xrd extends DocumentAbstract
{
	public function import(SimpleXMLElement $element)
	{
		if(isset($element->Subject))
		{
			$this->setSubject((string) $element->Subject);
		}

		if(isset($element->Expires))
		{
			$expires = new DateTime((string) $element->Expires);

			$this->setExpires($expires);
		}

		if(isset($element->Alias))
		{
			$aliases = array();
			foreach($element->Alias as $alias)
			{
				$aliases[] = (string) $alias;
			}

			$this->setAliases($aliases);
		}

		if(isset($element->Property))
		{
			$properties = array();
			foreach($element->Property as $property)
			{
				$name  = isset($property['type']) ? (string) $property['type'] : null;
				$value = (string) $property;

				if(!empty($name))
				{
					$value = $property->attributes('xsi', true)->nil;

					if($value == 'true')
					{
						$properties[$name] = null;
					}
					else
					{
						$properties[$name] = (string) $property;
					}
				}
			}

			$this->setProperties($properties);
		}

		if(isset($element->Link))
		{
			$links = array();
			foreach($element->Link as $link)
			{
				$links[] = $this->parseLink($link);
			}

			$this->setLinks($links);
		}
	}

	public function export()
	{
		$writer = new XMLWriter();
		$writer->openMemory();
		$writer->setIndent(true);
		$writer->startDocument('1.0', 'UTF-8');

		$writer->startElement('XRD');
		$writer->writeAttribute('xmlns', 'http://docs.oasis-open.org/ns/xri/xrd-1.0');
		$writer->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');

		if(!empty($this->subject))
		{
			$writer->writeElement('Subject', $this->subject);
		}

		if(!empty($this->expires))
		{
			$writer->writeElement('Expires', $this->expires->format(DateTime::RFC3339));
		}

		if(!empty($this->aliases))
		{
			foreach($this->aliases as $alias)
			{
				$writer->writeElement('Alias', $alias);
			}
		}

		if(!empty($this->properties))
		{
			foreach($this->properties as $name => $value)
			{
				$writer->startElement('Property');
				$writer->writeAttribute('type', $name);

				if($value === null)
				{
					$writer->writeAttribute('xsi:nil', 'true');
				}
				else
				{
					$writer->text($value);
				}

				$writer->endElement();
			}
		}

		if(!empty($this->links))
		{
			foreach($this->links as $link)
			{
				$this->buildLink($writer, $link);
			}
		}

		$writer->endElement();
		$writer->endDocument();

		return $writer->outputMemory();
	}

	protected function parseLink(SimpleXMLElement $element)
	{
		$link = new Link();

		if(isset($element['rel']))
		{
			$link->setRel((string) $element['rel']);
		}
		else
		{
			throw new Exception('Rel member must be present');
		}

		if(isset($element['type']))
		{
			$link->setType((string) $element['type']);
		}

		if(isset($element['href']))
		{
			$link->setHref((string) $element['href']);
		}
		else if(isset($element['template']))
		{
			$link->setTemplate((string) $element['template']);
		}

		if(isset($element->Title))
		{
			$titles = array();
			foreach($element->Title as $title)
			{
				$lang = (string) $title->attributes('xml', true)->lang;

				if(empty($lang))
				{
					$lang = 'default';
				}

				$titles[$lang] = (string) $title;
			}

			$link->setTitles($titles);
		}

		if(isset($element->Property))
		{
			$properties = array();
			foreach($element->Property as $property)
			{
				$type = isset($property['type']) ? (string) $property['type'] : null;

				if(!empty($type))
				{
					$value = $property->attributes('xsi', true)->nil;

					if($value == 'true')
					{
						$properties[$type] = null;
					}
					else
					{
						$properties[$type] = (string) $property;
					}
				}
			}

			$link->setProperties($properties);
		}

		return $link;
	}

	protected function buildLink(XMLWriter $writer, Link $link)
	{
		$writer->startElement('Link');
		$writer->writeAttribute('rel', $link->getRel());

		$type = $link->getType();

		if(!empty($type))
		{
			$writer->writeAttribute('type', $type);
		}

		$href     = $link->getHref();
		$template = $link->getTemplate();

		if(!empty($href))
		{
			$writer->writeAttribute('href', $href);
		}
		else if(!empty($template))
		{
			$writer->writeAttribute('template', $template);
		}

		$titles = $link->getTitles();
		if(!empty($titles))
		{
			foreach($titles as $lang => $title)
			{
				if($lang == 'default')
				{
					$writer->writeElement('Title', $title);
				}
				else
				{
					$writer->startElement('Title');
					$writer->writeAttribute('xml:lang', $lang);
					$writer->text($title);
					$writer->endElement();
				}
			}
		}

		$properties = $link->getProperties();
		if(!empty($properties))
		{
			foreach($properties as $type => $value)
			{
				if($value === null)
				{
					$writer->startElement('Property');
					$writer->writeAttribute('type', $type);
					$writer->writeAttribute('xsi:nil', 'true');
					$writer->endElement();
				}
				else
				{
					$writer->startElement('Property');
					$writer->writeAttribute('type', $type);
					$writer->text($value);
					$writer->endElement();
				}
			}
		}

		$writer->endElement();
	}
}

