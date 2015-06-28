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

namespace PSX\Atom\Writer;

use DateTime;
use InvalidArgumentException;
use PSX\Atom\Writer as Atom;
use PSX\Data\Reader\Xml as XmlReader;
use PSX\Data\RecordInterface;
use PSX\Data\Writer\Xml;
use PSX\Http\MediaType;
use XMLWriter;

/**
 * Entry
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Entry
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

						try
						{
							$mediaType = new MediaType($type);

							if(MediaType\Xml::isMediaType($mediaType))
							{
								if($content instanceof RecordInterface)
								{
									$writer = new Xml($this->writer);
									$writer->write($content);
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
						}
						catch(InvalidArgumentException $e)
						{
							// invalid media type
							$this->writer->text($content);
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
