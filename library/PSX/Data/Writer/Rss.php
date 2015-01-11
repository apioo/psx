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

namespace PSX\Data\Writer;

use DateTime;
use InvalidArgumentException;
use PSX\Data\RecordInterface;
use PSX\Data\ResultSet;
use PSX\Data\WriterInterface;
use PSX\Exception;
use PSX\Http\MediaType;
use PSX\Rss as RssRecord;
use PSX\Rss\Item;
use PSX\Rss\Writer;
use XMLWriter;

/**
 * Rss
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Rss implements WriterInterface
{
	public static $mime = 'application/rss+xml';

	public function write(RecordInterface $record)
	{
		if($record instanceof RssRecord)
		{
			$writer = new Writer($record->getTitle(), $record->getLink(), $record->getDescription());

			$this->buildChannel($record, $writer);

			foreach($record as $row)
			{
				$item = $writer->createItem();

				$this->buildItem($row, $item);

				$item->close();
			}

			return $writer->toString();
		}
		else if($record instanceof Item)
		{
			$writer = new Writer\Item();

			$this->buildItem($record, $writer);

			return $writer->toString();
		}
		else
		{
			throw new InvalidArgumentException('Record must be an PSX\Rss or PSX\Rss\Item record');
		}
	}

	public function isContentTypeSupported(MediaType $contentType)
	{
		return $contentType->getName() == self::$mime;
	}

	public function getContentType()
	{
		return self::$mime;
	}

	protected function buildChannel(RssRecord $rss, Writer $writer)
	{
		$language = $rss->getLanguage();
		if(!empty($language))
		{
			$writer->setLanguage($language);
		}

		$copyright = $rss->getCopyright();
		if(!empty($copyright))
		{
			$writer->setCopyright($copyright);
		}

		$managingEditor = $rss->getManagingEditor();
		if(!empty($managingEditor))
		{
			$writer->setManagingEditor($managingEditor);
		}

		$webMaster = $rss->getWebMaster();
		if(!empty($webMaster))
		{
			$writer->setWebMaster($webMaster);
		}

		$pubDate = $rss->getPubDate();
		if($pubDate instanceof DateTime)
		{
			$writer->setPubDate($pubDate);
		}

		$lastBuildDate = $rss->getLastBuildDate();
		if($lastBuildDate instanceof DateTime)
		{
			$writer->setLastBuildDate($lastBuildDate);
		}

		$categories = $rss->getCategory();
		if(is_array($categories))
		{
			foreach($categories as $category)
			{
				$writer->addCategory($category->getText(), $category->getDomain());
			}
		}

		$generator = $rss->getGenerator();
		if(!empty($generator))
		{
			$writer->setGenerator($generator);
		}

		$docs = $rss->getDocs();
		if(!empty($docs))
		{
			$writer->setDocs($docs);
		}

		$cloud = $rss->getCloud();
		if($cloud instanceof Cloud)
		{
			$writer->setCloud($cloud->getDomain(), 
				$cloud->getPort(), 
				$cloud->getPath(), 
				$cloud->getRegisterProcedure(), 
				$cloud->getProtocol());
		}

		$ttl = $rss->getTtl();
		if(!empty($ttl))
		{
			$writer->setTtl($ttl);
		}

		$image = $rss->getImage();
		if(!empty($image))
		{
			$writer->setImage($image);
		}

		$rating = $rss->getRating();
		if(!empty($rating))
		{
			$writer->setRating($rating);
		}

		$skipHours = $rss->getSkipHours();
		if(!empty($skipHours))
		{
			$writer->setSkipHours($skipHours);
		}

		$skipDays = $rss->getSkipDays();
		if(!empty($skipDays))
		{
			$writer->setSkipDays($skipDays);
		}
	}

	protected function buildItem(Item $item, Writer\Item $writer)
	{
		$title = $item->getTitle();
		if(!empty($title))
		{
			$writer->setTitle($title);
		}

		$link = $item->getLink();
		if(!empty($link))
		{
			$writer->setLink($link);
		}

		$description = $item->getDescription();
		if(!empty($description))
		{
			$writer->setDescription($description);
		}

		$author = $item->getAuthor();
		if(!empty($author))
		{
			$writer->setAuthor($author);
		}

		$categories = $item->getCategory();
		if(is_array($categories))
		{
			foreach($categories as $category)
			{
				$writer->addCategory($category->getText(), $category->getDomain());
			}
		}

		$comments = $item->getComments();
		if(!empty($comments))
		{
			$writer->setComments($comments);
		}

		$enclosure = $item->getEnclosure();
		if($enclosure instanceof Enclosure)
		{
			$writer->setEnclosure($enclosure->getUrl(), $enclosure->getLength(), $enclosure->getType());
		}

		$guid = $item->getGuid();
		if(!empty($guid))
		{
			$writer->setGuid($guid);
		}

		$pubDate = $item->getPubDate();
		if($pubDate instanceof DateTime)
		{
			$writer->setPubDate($pubDate);
		}

		$source = $item->getSource();
		if(!empty($source))
		{
			$writer->setSource($source);
		}
	}
}

