<?php
/*
 *  $Id: Atom.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Data\Writer;

use DateTime;
use PSX\Data\RecordInterface;
use PSX\Data\ResultSet;
use PSX\Data\Writer\Atom\Entry;
use PSX\Data\WriterInterface;
use PSX\Data\WriterResult;
use XMLWriter;

/**
 * PSX_Data_Writer_Atom
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Data
 * @version    $Revision: 480 $
 */
class Atom implements WriterInterface
{
	public static $mime = 'application/atom+xml';
	public static $xmlns = 'http://www.w3.org/2005/Atom';

	private $title;
	private $id;
	private $updated;

	public $writer;
	public $writerResult;

	public function __construct()
	{
		$this->writer = new XMLWriter();
		$this->writer->openMemory();
		$this->writer->setIndent(true);
		$this->writer->startDocument('1.0', 'UTF-8');
	}

	public function setConfig($title, $id, DateTime $updated)
	{
		$this->writer->startElement('feed');
		$this->writer->writeAttribute('xmlns', self::$xmlns);

		$this->setTitle($title);
		$this->setId($id);
		$this->setUpdated($updated);
	}

	public function addAuthor($name, $uri = false, $email = false)
	{
		$this->writer->startElement('author');

		self::personConstruct($this->writer, $name, $uri, $email);

		$this->writer->endElement();
	}

	public function addCategory($term, $scheme = false, $label = false)
	{
		self::categoryConstruct($term, $scheme, $label);
	}

	public function addContributor($name, $uri = false, $email = false)
	{
		$this->writer->startElement('contributor');

		self::personConstruct($this->writer, $name, $uri, $email);

		$this->writer->endElement();
	}

	public function setGenerator($generator, $uri = false, $version = false)
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

	public function addLink($href, $rel = false, $type = false, $hreflang = false, $title = false, $length = false)
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

	public function add(Entry $entry)
	{
		$entry->close();
	}

	public function close()
	{
		$this->writer->endElement();
		$this->writer->endDocument();

		echo $this->writer->outputMemory();
	}

	public function createEntry()
	{
		return new Entry($this->writer);
	}

	public function write(RecordInterface $record)
	{
		$this->writerResult = new WriterResult(WriterInterface::ATOM, $this);

		if($record instanceof ResultSet)
		{
			foreach($record->entry as $entry)
			{
				$entry = $entry->export($this->writerResult);

				$this->add($entry);
			}

			$this->close();
		}
		else
		{
			$record->export($this->writerResult);

			$this->close();
		}
	}

	public static function personConstruct(XMLWriter $writer, $name, $uri = false, $email = false)
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

	public static function linkConstruct(XMLWriter $writer, $href, $rel = false, $type = false, $hreflang = false, $title = false, $length = false)
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
