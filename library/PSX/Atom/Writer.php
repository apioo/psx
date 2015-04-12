<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Atom;

use DateTime;
use XMLWriter;

/**
 * Writer
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Writer
{
	public static $mime  = 'application/atom+xml';
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
		self::categoryConstruct($this->writer, $term, $scheme, $label);
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
		return '<link rel="alternate" type="' . self::$mime . '" title="' . htmlspecialchars($title) . '" href="' . htmlspecialchars($href) . '" />';
	}
}
