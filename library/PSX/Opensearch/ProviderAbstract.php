<?php
/*
 *  $Id: ProviderAbstract.php 620 2012-08-25 11:17:36Z k42b3.x@googlemail.com $
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

/**
 * PSX_Opensearch_ProviderAbstract
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Opensearch
 * @version    $Revision: 620 $
 */
abstract class PSX_Opensearch_ProviderAbstract extends PSX_ModuleAbstract
{
	public static $mime  = 'application/opensearchdescription+xml';
	public static $xmlns = 'http://a9.com/-/spec/opensearch/1.1/';

	public static $rel   = array('results', 'suggestions', 'self', 'collection');
	public static $type  = array('image/png', 'image/jpeg', 'image/x-icon', 'image/vnd.microsoft.icon');
	public static $right = array('open', 'limited', 'private', 'closed');

	private $writer;

	protected function setConfig($name, $description)
	{
		header('Content-type: ' .  self::$mime);

		$this->writer = new XMLWriter();
		$this->writer->openURI('php://output');
		$this->writer->setIndent(true);
		$this->writer->startDocument('1.0', 'UTF-8');
		$this->writer->startElement('OpenSearchDescription');
		$this->writer->writeAttribute('xmlns', self::$xmlns);

		$this->setName($name);
		$this->setDescription($description);
	}

	protected function setName($name)
	{
		$name = htmlspecialchars($name);

		if(strlen($name) > 16)
		{
			throw new PSX_Opensearch_Exception('The value must contain 16 or fewer characters');
		}
		else
		{
			$this->writer->writeElement('ShortName', $name);
		}
	}

	protected function setDescription($description)
	{
		$description = htmlspecialchars($description);

		if(strlen($description) > 1024)
		{
			throw new PSX_Opensearch_Exception('The value must contain 1024 or fewer characters');
		}
		else
		{
			$this->writer->startElement('Description');
			$this->writer->text($description);
			$this->writer->endElement();
		}
	}

	protected function addUrl($template, $type, $rel = false, $indexOffset = false, $pageOffset = false)
	{
		$this->writer->startElement('Url');
		$this->writer->writeAttribute('template', $template);
		$this->writer->writeAttribute('type', $type);

		if(!empty($rel))
		{
			if(!in_array($rel, self::$rel))
			{
				throw new PSX_Opensearch_Exception('rel must be one of the following values: ' . implode(', ', $this->rel));
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

	protected function setContact($contact)
	{
		$this->writer->writeElement('Contact', $contact);
	}

	protected function setTags(array $tags)
	{
		$tags = htmlspecialchars(implode(' ', $tags));

		if(strlen($tags) > 256)
		{
			throw new PSX_Opensearch_Exception('The value must contain 256 or fewer characters');
		}
		else
		{
			$this->writer->writeElement('Tags', $tags);
		}
	}

	protected function setLongName($longName)
	{
		$longName = htmlspecialchars($longName);

		if(strlen($longName) > 48)
		{
			throw new PSX_Opensearch_Exception('The value must contain 48 or fewer characters');
		}
		else
		{
			$this->writer->writeElement('LongName', $longName);
		}
	}

	protected function addImage($url, $height, $width, $type = false)
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
				throw new PSX_Opensearch_Exception('Invalid mime type allowed: ' . implode(', ', $this->mime));
			}
			else
			{
				$this->writer->writeAttribute('type', $type);
			}
		}

		$this->writer->text($url);
		$this->writer->endElement();
	}

	protected function addQuery($role, $searchTerms)
	{
		$this->writer->startElement('Query');
		$this->writer->writeAttribute('role', $role);
		$this->writer->writeAttribute('searchTerms', $searchTerms);
		$this->writer->endElement();
	}

	protected function setDeveloper($developer)
	{
		$this->writer->writeElement('Developer', $developer);
	}

	protected function setAttribution($attribution)
	{
		$this->writer->writeElement('Attribution', $attribution);
	}

	protected function setSyndicationRight($syndicationRight)
	{
		if(!in_array($syndicationRight, self::$right))
		{
			throw new PSX_Opensearch_Exception('Invalid syndication right allowed: ' . implode(', ', $this->syndication_right));
		}
		else
		{
			$this->writer->writeElement('SyndicationRight', $syndicationRight);
		}
	}

	protected function setAdultContent($adultContent)
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

	protected function addLanguage($language)
	{
		$this->writer->writeElement('Language', $language);
	}

	protected function addInputEncoding($inputEncoding)
	{
		$this->writer->writeElement('InputEncoding', $inputEncoding);
	}

	protected function addOutputEncoding($outputEncoding)
	{
		$this->writer->writeElement('OutputEncoding', $outputEncoding);
	}

	protected function close()
	{
		$this->writer->endElement();
		$this->writer->endDocument();
		$this->writer->flush();
	}

	public static function link($title, $href)
	{
		return '<link rel="search" type="' . self::$mime . '" title="' . $title . '" href="' . $href . '" />';
	}
}
