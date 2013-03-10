<?php
/*
 *  $Id: Entry.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

namespace PSX\Data\Writer\Atom;

use DateTime;
use PSX\Data\RecordInterface;
use PSX\Data\Writer;
use XMLWriter;

/**
 * PSX_Data_Writer_Atom_Entry
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Data
 * @version    $Revision: 480 $
 */
class Entry extends Writer\Xml
{
	public $writer;

	public function __construct(XMLWriter $writer)
	{
		$this->writer = $writer;
		$this->writer->startElement('entry');
		$this->writer->writeAttribute('xmlns', Writer\Atom::$xmlns);
	}

	public function addAuthor($name, $uri = false, $email = false)
	{
		$this->writer->startElement('author');

		Writer\Atom::personConstruct($this->writer, $name, $uri, $email);

		$this->writer->endElement();
	}

	public function addCategory($term, $scheme = false, $label = false)
	{
		Writer\Atom::categoryConstruct($this->writer, $term, $scheme, $label);
	}

	public function setContent($content, $type, $src = null)
	{
		$this->writer->startElement('content');

		if(empty($src))
		{
			switch($type)
			{
				case 'application/xml':

					$this->writer->writeAttribute('type', $type);

					if($content instanceof RecordInterface)
					{
						$this->recXmlEncode($content->getName(), $content->getData());
					}
					else
					{
						$this->writer->writeRaw($content);
					}

					break;

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

					$this->writer->text($content);

					break;
			}
		}
		else
		{
			$this->writer->writeAttribute('src', $src);
		}

		$this->writer->endElement();
	}

	public function addContributor($name, $uri = false, $email = false)
	{
		$this->writer->startElement('contributor');

		Writer\Atom::personConstruct($this->writer, $name, $uri, $email);

		$this->writer->endElement();
	}

	public function setId($id)
	{
		$this->writer->writeElement('id', $id);
	}

	public function addLink($href, $rel = false, $type = false, $hreflang = false, $title = false, $length = false)
	{
		Writer\Atom::linkConstruct($this->writer, $href, $rel, $type, $hreflang, $title, $length);
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
		Writer\Atom::textConstruct($this->writer, 'summary', $summary, $type);
	}

	public function setTitle($title, $type = null)
	{
		Writer\Atom::textConstruct($this->writer, 'title', $title, $type);
	}

	public function setUpdated(DateTime $updated)
	{
		$this->writer->writeElement('updated', $updated->format(DateTime::ATOM));
	}

	public function close()
	{
		$this->writer->endElement();
	}
}
