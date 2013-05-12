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

namespace PSX\Data;

use PSX\ActivityStream;
use PSX\Data\NotSupportedException;
use PSX\Data\RecordAbstract;
use PSX\Data\WriterResult;
use PSX\Data\WriterInterface;
use PSX\DateTime;

/**
 * WriterTestRecord
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class WriterTestRecord extends RecordAbstract
{
	public $id;
	public $author;
	public $title;
	public $content;
	public $date;

	public function getName()
	{
		return 'record';
	}

	public function getFields()
	{
		return array(

			'id'      => $this->id,
			'author'  => $this->author,
			'title'   => $this->title,
			'content' => $this->content,
			'date'    => $this->date,

		);
	}

	public function getDate()
	{
		return new DateTime($this->date);
	}

	public function export(WriterResult $result)
	{
		switch($result->getType())
		{
			case WriterInterface::JSON:
			case WriterInterface::XML:
			case WriterInterface::FORM:
				return $this->getData();
				break;

			case WriterInterface::ATOM:
				$entry = $result->getWriter()->createEntry();
				$entry->setTitle($this->title);
				$entry->setId($this->id);
				$entry->setUpdated($this->getDate());
				$entry->addAuthor($this->author);
				$entry->setContent($this->content, 'html');
				return $entry;
				break;

			case WriterInterface::RSS:
				$item = $result->getWriter()->createItem();
				$item->setTitle($this->title);
				$item->setGuid($this->id);
				$item->setPubDate($this->getDate());
				$item->setAuthor($this->author);
				$item->setDescription($this->content);
				return $item;
				break;

			case WriterInterface::JAS:
				$actor = new ActivityStream\Object();
				$actor->setObjectType('person');
				$actor->setDisplayName($this->author);

				$object = new ActivityStream\Object();
				$object->setDisplayName($this->title);
				$object->setId($this->id);
				$object->setObjectType('article');
				$object->setPublished($this->getDate());
				$object->setContent($this->content);

				$activity = new ActivityStream\Activity();
				$activity->setActor($actor);
				$activity->setVerb('post');
				$activity->setObject($object);
				return $activity;
				break;

			default:
				throw new NotSupportedException('Writer is not supported');
				break;
		}
	}
}
