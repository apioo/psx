<?php
/*
 *  $Id: WriterTestRecord.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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
 * PSX_Data_WriterTestRecord
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class PSX_Data_WriterTestRecord extends PSX_Data_RecordAbstract
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

	public function export(PSX_Data_WriterResult $result)
	{
		switch($result->getType())
		{
			case PSX_Data_WriterInterface::JSON:
			case PSX_Data_WriterInterface::XML:
			case PSX_Data_WriterInterface::FORM:

				return $this->getData();

				break;

			case PSX_Data_WriterInterface::ATOM:

				$entry = $result->getWriter()->createEntry();

				$entry->setTitle($this->title);
				$entry->setId($this->id);
				$entry->setUpdated($this->getDate());
				$entry->addAuthor($this->author);
				$entry->setContent($this->content, 'html');

				return $entry;

				break;

			case PSX_Data_WriterInterface::RSS:

				$item = $result->getWriter()->createItem();

				$item->setTitle($this->title);
				$item->setGuid($this->id);
				$item->setPubDate($this->getDate());
				$item->setAuthor($this->author);
				$item->setDescription($this->content);

				return $item;

				break;

			default:

				throw new PSX_Data_Exception('Writer is not supported');

				break;
		}
	}
}
