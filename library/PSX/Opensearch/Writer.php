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

namespace PSX\Opensearch;

use PSX\Exception;
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
	public static $mime  = 'application/opensearchdescription+xml';
	public static $xmlns = 'http://a9.com/-/spec/opensearch/1.1/';

	public static $rel   = array('results', 'suggestions', 'self', 'collection');
	public static $type  = array('image/png', 'image/jpeg', 'image/x-icon', 'image/vnd.microsoft.icon');
	public static $right = array('open', 'limited', 'private', 'closed');

	protected $writer;

	public function __construct($name, $description, XMLWriter $writer = null)
	{
		$this->writer = $writer === null ? new XMLWriter() : $writer;

		if($writer === null)
		{
			$this->writer->openMemory();
			$this->writer->setIndent(true);
			$this->writer->startDocument('1.0', 'UTF-8');
		}

		$this->writer->startElement('OpenSearchDescription');
		$this->writer->writeAttribute('xmlns', self::$xmlns);

		$this->setName($name);
		$this->setDescription($description);
	}

	public function setName($name)
	{
		$name = htmlspecialchars($name);

		if(strlen($name) > 16)
		{
			throw new Exception('The value must contain 16 or fewer characters');
		}
		else
		{
			$this->writer->writeElement('ShortName', $name);
		}
	}

	public function setDescription($description)
	{
		$description = htmlspecialchars($description);

		if(strlen($description) > 1024)
		{
			throw new Exception('The value must contain 1024 or fewer characters');
		}
		else
		{
			$this->writer->startElement('Description');
			$this->writer->text($description);
			$this->writer->endElement();
		}
	}

	public function addUrl($template, $type, $rel = false, $indexOffset = false, $pageOffset = false)
	{
		$this->writer->startElement('Url');
		$this->writer->writeAttribute('template', $template);
		$this->writer->writeAttribute('type', $type);

		if(!empty($rel))
		{
			if(!in_array($rel, self::$rel))
			{
				throw new Exception('rel must be one of the following values: ' . implode(', ', self::$rel));
			}
			else
			{
				$this->writer->writeAttribute('rel', $rel);
			}
		}

		if(!empty($indexOffset))
		{
			$this->writer->writeAttribute('indexOffset', intval($indexOffset));
		}

		if(!empty($pageOffset))
		{
			$this->writer->writeAttribute('pageOffset', intval($pageOffset));
		}

		$this->writer->endElement();
	}

	public function setContact($contact)
	{
		$this->writer->writeElement('Contact', $contact);
	}

	public function setTags(array $tags)
	{
		$tags = htmlspecialchars(implode(' ', $tags));

		if(strlen($tags) > 256)
		{
			throw new Exception('The value must contain 256 or fewer characters');
		}
		else
		{
			$this->writer->writeElement('Tags', $tags);
		}
	}

	public function setLongName($longName)
	{
		$longName = htmlspecialchars($longName);

		if(strlen($longName) > 48)
		{
			throw new Exception('The value must contain 48 or fewer characters');
		}
		else
		{
			$this->writer->writeElement('LongName', $longName);
		}
	}

	public function addImage($url, $height, $width, $type = false)
	{
		$this->writer->startElement('Image');

		if(!empty($height))
		{
			$this->writer->writeAttribute('height', $height);
		}

		if(!empty($width))
		{
			$this->writer->writeAttribute('width', $width);
		}

		if(!empty($type))
		{
			if(!in_array($type, self::$type))
			{
				throw new Exception('Invalid mime type allowed: ' . implode(', ', self::$mime));
			}
			else
			{
				$this->writer->writeAttribute('type', $type);
			}
		}

		$this->writer->text($url);
		$this->writer->endElement();
	}

	public function addQuery($role, $searchTerms)
	{
		$this->writer->startElement('Query');
		$this->writer->writeAttribute('role', $role);
		$this->writer->writeAttribute('searchTerms', $searchTerms);
		$this->writer->endElement();
	}

	public function setDeveloper($developer)
	{
		$this->writer->writeElement('Developer', $developer);
	}

	public function setAttribution($attribution)
	{
		$this->writer->writeElement('Attribution', $attribution);
	}

	public function setSyndicationRight($syndicationRight)
	{
		if(!in_array($syndicationRight, self::$right))
		{
			throw new Exception('Invalid syndication right allowed: ' . implode(', ', self::$right));
		}
		else
		{
			$this->writer->writeElement('SyndicationRight', $syndicationRight);
		}
	}

	public function setAdultContent($adultContent)
	{
		if(!$adultContent)
		{
			$adultContent = 'false';
		}
		else
		{
			$adultContent = 'true';
		}

		$this->writer->writeElement('AdultContent', $adultContent);
	}

	public function setLanguage($language)
	{
		$this->writer->writeElement('Language', $language);
	}

	public function setInputEncoding($inputEncoding)
	{
		$this->writer->writeElement('InputEncoding', $inputEncoding);
	}

	public function setOutputEncoding($outputEncoding)
	{
		$this->writer->writeElement('OutputEncoding', $outputEncoding);
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

	public static function link($title, $href)
	{
		return '<link rel="search" type="' . self::$mime . '" title="' . $title . '" href="' . $href . '" />';
	}
}
