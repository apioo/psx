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

namespace PSX\Oembed;

use PSX\Data\RecordAbstract;
use PSX\Data\ReaderInterface;
use PSX\Data\ReaderResult;
use PSX\Data\InvalidDataException;
use PSX\Data\NotSupportedException;

/**
 * TypeAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class TypeAbstract extends RecordAbstract
{
	public $type;
	public $version;
	public $title;
	public $authorName;
	public $authorUrl;
	public $providerName;
	public $providerUrl;
	public $cacheAge;
	public $thumbnailUrl;
	public $thumbnailWidth;
	public $thumbnailHeight;

	public function getFields()
	{
		$fields = array(

			'type'             => $this->type,
			'version'          => $this->version,
			'title'            => $this->title,
			'author_name'      => $this->authorName,
			'author_url'       => $this->authorUrl,
			'provider_name'    => $this->providerName,
			'provider_url'     => $this->providerUrl,
			'cache_age'        => $this->cacheAge,
			'thumbnail_url'    => $this->thumbnailUrl,
			'thumbnail_width'  => $this->thumbnailWidth,
			'thumbnail_height' => $this->thumbnailHeight,

		);

		return $fields;
	}

	public function setType($type)
	{
		if(!in_array($type, array('link', 'photo', 'rich', 'video')))
		{
			throw new InvalidDataException('Invalid type');
		}

		$this->type = $type;
	}

	public function setVersion($version)
	{
		if($version != '1.0')
		{
			throw new InvalidDataException('Invalid version');
		}

		$this->version = $version;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function setAuthorName($authorName)
	{
		$this->authorName = $authorName;
	}

	public function setAuthorUrl($authorUrl)
	{
		$this->authorUrl = $authorUrl;
	}

	public function setProviderName($providerName)
	{
		$this->providerName = $providerName;
	}

	public function setProviderUrl($providerUrl)
	{
		$this->providerUrl = $providerUrl;
	}

	public function setCacheAge($cacheAge)
	{
		$this->cacheAge = (integer) $cacheAge;
	}

	public function setThumbnailUrl($thumbnailUrl)
	{
		$this->thumbnailUrl = $thumbnailUrl;
	}

	public function setThumbnailWidth($thumbnailWidth)
	{
		$this->thumbnailWidth = $thumbnailWidth;
	}

	public function setThumbnailHeight($thumbnailHeight)
	{
		$this->thumbnailHeight = $thumbnailHeight;
	}

	public static function factory(ReaderResult $result)
	{
		switch($result->getType())
		{
			case ReaderInterface::JSON:
			case ReaderInterface::XML:

				$data  = $result->getData();
				$type  = isset($data['type']) ? strtolower($data['type']) : null;

				if(!in_array($type, array('link', 'photo', 'rich', 'video')))
				{
					throw new InvalidDataException('Invalid type');
				}

				$class = '\PSX\Oembed\Type\\' . ucfirst($type);

				if(class_exists($class))
				{
					$type = new $class();
					$type->import($result);

					return $type;
				}
				else
				{
					throw new InvalidDataException('Type class not found');
				}

				break;

			default:

				throw new NotSupportedException('Invalid reader');
				break;
		}
	}
}
