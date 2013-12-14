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

namespace PSX\Atom;

use DateTime;
use PSX\Xml\WriterInterface;
use XMLWriter;

/**
 * Writer
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Writer implements WriterInterface
{
	public static $mime = 'application/atom+xml';
	public static $xmlns = 'http://www.w3.org/2005/Atom';

	protected $writer;

	public function __construct($title, $id, DateTime $updated, XMLWriter $writer = null, $root = 'feed', $xmlns = null)
	{
		$this->writer = $writer === null ? new XMLWriter() : $writer;

		if($writer === null)
		{
			$this->writer->openMemory();
			$this->writer->setIndent(true);
			$this->writer->startDocument('1.0', 'UTF-8');
		}

		$this->writer->startElement($root);

		if($xmlns === null)
		{
			$this->writer->writeAttribute('xmlns', self::$xmlns);
		}

		$this->setTitle($title);
		$this->setId($id);
		$this->setUpdated($updated);
	}

	public function addAuthor($name, $uri = null, $email = null)
	{
		$this->writer->startElement('author');

		self::personConstruct($this->writer, $name, $uri, $email);

		$this->writer->endElement();
	}

	public function addCategory($term, $scheme = null, $label = null)
	{
		self::categoryConstruct($term, $scheme, $label);
	}

	public function addContributor($name, $uri = null, $email = null)
	{
		$this->writer->startElement('contributor');

		self::personConstruct($this->writer, $name, $uri, $email);

		$this->writer->endElement();
	}

	public function setGenerator($generator, $uri = null, $version = null)
	{
		$this->writer->startElement('generator');

		if(!empty($uri))
		{
			$this->writer->writeAttribute('uri', $uri);
		}

		if(!empty($version))
		{
			$this->writer->writeAttribute('version', $version);
		}

		$this->writer->text($generator);
		$this->writer->endElement();
	}

	public function setIcon($icon)
	{
		$this->writer->writeElement('icon', $icon);
	}

	public function setLogo($logo)
	{
		$this->writer->writeElement('logo', $logo);
	}

	public function setId($id)
	{
		$this->writer->writeElement('id', $id);
	}

	public function addLink($href, $rel = null, $type = null, $hreflang = null, $title = null, $length = null)
	{
		self::linkConstruct($this->writer, $href, $rel, $type, $hreflang, $title, $length);
	}

	public function setRights($rights)
	{
		$this->writer->writeElement('rights', $rights);
	}

	public function setSubTitle($type, $subTitle)
	{
		$this->writer->startElement('subtitle');
		$this->writer->writeAttribute('type', $type);
		$this->writer->text($subTitle);
		$this->writer->endElement();
	}

	public function setTitle($title)
	{
		$this->writer->writeElement('title', $title);
	}

	public function setUpdated(DateTime $updated)
	{
		$this->writer->writeElement('updated', $updated->format(DateTime::ATOM));
	}

	public function createEntry()
	{
		return new Writer\Entry($this->writer);
	}

	public function close()
	{
		$this->writer->endElement();
	}

	public function output()
	{
		header('Content-Type: ' . self::$mime);

		echo $this->toString();
	}

	public function toString()
	{
		$this->close();
		$this->writer->endDocument();

		return $this->writer->outputMemory();		
	}

	public function getWriter()
	{
		return $this->writer;
	}

	public static function personConstruct(XMLWriter $writer, $name, $uri = null, $email = null)
	{
		$writer->writeElement('name', $name);

		if(!empty($uri))
		{
			$writer->writeElement('uri', $uri);
		}

		if(!empty($email))
		{
			$writer->writeElement('email', $email);
		}
	}

	public static function categoryConstruct(XMLWriter $writer, $term, $scheme, $label)
	{
		$writer->startElement('category');
		$writer->writeAttribute('term', $term);

		if(!empty($scheme))
		{
			$writer->writeAttribute('scheme', $scheme);
		}

		if(!empty($label))
		{
			$writer->writeAttribute('label', $label);
		}

		$writer->endElement();
	}

	public static function linkConstruct(XMLWriter $writer, $href, $rel = null, $type = null, $hreflang = null, $title = null, $length = null)
	{
		$writer->startElement('link');
		$writer->writeAttribute('href', $href);

		if(!empty($rel))
		{
			$writer->writeAttribute('rel', $rel);
		}

		if(!empty($type))
		{
			$writer->writeAttribute('type', $type);
		}

		if(!empty($hreflang))
		{
			$writer->writeAttribute('hreflang', $hreflang);
		}

		if(!empty($title))
		{
			$writer->writeAttribute('title', $title);
		}

		if(!empty($length))
		{
			$writer->writeAttribute('length', $length);
		}

		$writer->endElement();
	}

	public static function textConstruct(XMLWriter $writer, $element, $content, $type = null)
	{
		$writer->startElement($element);

		switch($type)
		{
			case 'text':
			case 'html':
				$writer->writeAttribute('type', $type);
				$writer->text($content);
				break;

			case 'xhtml':
				$writer->writeAttribute('type', $type);
				$writer->writeRaw($content);
				break;

			default:
				$writer->text($content);
				break;
		}

		$writer->endElement();
	}

	public static function link($title, $href)
	{
		return '<link rel="alternate" type="' . self::$mime . '" title="' . $title . '" href="' . $href . '" />';
	}
}
