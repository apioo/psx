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

namespace PSX\Data\Writer;

use DateTime;
use InvalidArgumentException;
use PSX\Atom as AtomRecord;
use PSX\Atom\Entry;
use PSX\Atom\Text;
use PSX\Atom\Writer;
use PSX\Data\RecordInterface;
use PSX\Data\ResultSet;
use PSX\Data\WriterInterface;
use PSX\Exception;
use XMLWriter;

/**
 * Atom
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Atom implements WriterInterface
{
	public static $mime = 'application/atom+xml';

	public function write(RecordInterface $record)
	{
		if($record instanceof AtomRecord)
		{
			$writer = new Writer($record->getTitle(), $record->getId(), $record->getUpdated());

			$this->buildFeed($record, $writer);

			foreach($record as $row)
			{
				$entry = $writer->createEntry();

				$this->buildEntry($row, $entry);

				$entry->close();
			}

			return $writer->toString();
		}
		else if($record instanceof Entry)
		{
			$writer = new Writer\Entry();

			$this->buildEntry($record, $writer);

			return $writer->toString();
		}
		else
		{
			throw new InvalidArgumentException('Record must be an PSX\Atom or PSX\Atom\Entry record');
		}
	}

	public function isContentTypeSupported($contentType)
	{
		return stripos($contentType, self::$mime) !== false;
	}

	public function getContentType()
	{
		return self::$mime;
	}

	protected function buildFeed(AtomRecord $atom, Writer $writer)
	{
		$subTitle = $atom->getSubTitle();
		if(!empty($subTitle))
		{
			$writer->setSubTitle($subTitle->getType(), $subTitle->getContent());
		}

		foreach($atom->getLink() as $link)
		{
			$writer->addLink($link->getHref(), $link->getRel(), $link->getType(), $link->getHrefLang(), $link->getTitle(), $link->getLength());
		}

		$rights = $atom->getRights();
		if(!empty($rights))
		{
			$writer->setRights($rights);
		}

		$generator = $atom->getGenerator();
		if(!empty($generator))
		{
			$writer->setGenerator($generator->getText(), $generator->getUri(), $generator->getVersion());
		}

		foreach($atom->getAuthor() as $author)
		{
			$writer->addAuthor($author->getName(), $author->getUri(), $author->getEmail());
		}

		foreach($atom->getCategory() as $category)
		{
			$writer->addCategory($category->getTerm(), $category->getScheme(), $category->getLabel());
		}

		foreach($atom->getContributor() as $contributor)
		{
			$writer->addContributor($contributor->getName(), $contributor->getUri(), $contributor->getEmail());
		}

		$icon = $atom->getIcon();
		if(!empty($icon))
		{
			$writer->setIcon($icon);
		}

		$logo = $atom->getLogo();
		if(!empty($logo))
		{
			$writer->setLogo($logo);
		}
	}

	protected function buildEntry(Entry $entry, Writer\Entry $writer)
	{
		$id = $entry->getId();
		if(!empty($id))
		{
			$writer->setId($id);
		}

		$title = $entry->getTitle();
		if(!empty($title))
		{
			$writer->setTitle($title);
		}

		$updated = $entry->getUpdated();
		if($updated instanceof DateTime)
		{
			$writer->setUpdated($updated);
		}

		$published = $entry->getPublished();
		if($published instanceof DateTime)
		{
			$writer->setPublished($published);
		}

		foreach($entry->getLink() as $link)
		{
			$writer->addLink($link->getHref(), $link->getRel(), $link->getType(), $link->getHrefLang(), $link->getTitle(), $link->getLength());
		}

		$rights = $entry->getRights();
		if(!empty($rights))
		{
			$entry->setRights($rights);
		}

		foreach($entry->getAuthor() as $author)
		{
			$writer->addAuthor($author->getName(), $author->getUri(), $author->getEmail());
		}

		foreach($entry->getCategory() as $category)
		{
			$writer->addCategory($category->getTerm(), $category->getScheme(), $category->getLabel());
		}

		foreach($entry->getContributor() as $contributor)
		{
			$writer->addContributor($contributor->getName(), $contributor->getUri(), $contributor->getEmail());
		}

		$content = $entry->getContent();
		if($content instanceof Text)
		{
			$writer->setContent($content->getContent(), $content->getType());
		}

		$summary = $entry->getSummary();
		if($summary instanceof Text)
		{
			$writer->setSummary($summary->getContent(), $summary->getType());
		}

		$source = $entry->getSource();
		if($source instanceof AtomRecord)
		{
			$sourceWriter = new Writer($source->getTitle(), $source->getId(), $source->getUpdated(), $writer->getWriter(), 'source', false);

			$this->buildFeed($source, $sourceWriter);

			$sourceWriter->close();
		}
	}
}
