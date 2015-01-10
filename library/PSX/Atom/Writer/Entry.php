<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Atom\Writer;

use DateTime;
use PSX\Atom\Writer as Atom;
use PSX\Data\RecordInterface;
use PSX\Data\Reader\Xml as XmlReader;
use PSX\Xml\Writer as Xml;
use PSX\Xml\WriterInterface;
use XMLWriter;

/**
 * Entry
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Entry implements WriterInterface
{
	protected $writer;

	public function __construct(XMLWriter $writer = null)
	{
		$this->writer = $writer === null ? new XMLWriter() : $writer;

		if($writer === null)
		{
			$this->writer->openMemory();
			$this->writer->setIndent(true);
			$this->writer->startDocument('1.0', 'UTF-8');
		}

		$this->writer->startElement('entry');

		if($writer === null)
		{
			$this->writer->writeAttribute('xmlns', Atom::$xmlns);
		}
	}

	public function addAuthor($name, $uri = false, $email = false)
	{
		$this->writer->startElement('author');

		Atom::personConstruct($this->writer, $name, $uri, $email);

		$this->writer->endElement();
	}

	public function addCategory($term, $scheme = false, $label = false)
	{
		Atom::categoryConstruct($this->writer, $term, $scheme, $label);
	}

	public function setContent($content, $type, $src = null)
	{
		$this->writer->startElement('content');

		if(empty($src))
		{
			switch($type)
			{
				case 'text':
				case 'html':

					$this->writer->writeAttribute('type', $type);
					$this->writer->text($content);
					break;

				case 'xhtml':

					$this->writer->writeAttribute('type', $type);
					$this->writer->writeRaw($content);
					break;

				default:

					if(!empty($type))
					{
						$this->writer->writeAttribute('type', $type);
					}

					if(XmlReader::isXmlMediaContentType($type))
					{
						if($content instanceof RecordInterface)
						{
							$info = $content->getRecordInfo();

							$xml = new Xml($this->writer);
							$xml->setRecord($info->getName(), $info->getData());
							$xml->close();
						}
						else
						{
							$this->writer->writeRaw($content);
						}
					}
					else
					{
						$this->writer->text($content);
					}

					break;
			}
		}
		else
		{
			$this->writer->writeAttribute('src', $src);

			if(!empty($type))
			{
				$this->writer->writeAttribute('type', $type);
			}
		}

		$this->writer->endElement();
	}

	public function addContributor($name, $uri = false, $email = false)
	{
		$this->writer->startElement('contributor');

		Atom::personConstruct($this->writer, $name, $uri, $email);

		$this->writer->endElement();
	}

	public function setId($id)
	{
		$this->writer->writeElement('id', $id);
	}

	public function addLink($href, $rel = false, $type = false, $hreflang = false, $title = false, $length = false)
	{
		Atom::linkConstruct($this->writer, $href, $rel, $type, $hreflang, $title, $length);
	}

	public function setPublished(DateTime $published)
	{
		$this->writer->writeElement('published', $published->format(DateTime::ATOM));
	}

	public function setRights($rights)
	{
		$this->writer->writeElement('rights', $rights);
	}

	public function setSummary($summary, $type = null)
	{
		Atom::textConstruct($this->writer, 'summary', $summary, $type);
	}

	public function setTitle($title, $type = null)
	{
		Atom::textConstruct($this->writer, 'title', $title, $type);
	}

	public function setUpdated(DateTime $updated)
	{
		$this->writer->writeElement('updated', $updated->format(DateTime::ATOM));
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
}
