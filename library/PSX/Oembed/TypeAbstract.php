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

namespace PSX\Oembed;

use PSX\Data\RecordAbstract;
use PSX\Data\Record\DefaultImporter;
use PSX\Data\RecordInfo;

/**
 * TypeAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class TypeAbstract extends RecordAbstract
{
	protected $type;
	protected $version;
	protected $title;
	protected $authorName;
	protected $authorUrl;
	protected $providerName;
	protected $providerUrl;
	protected $cacheAge;
	protected $thumbnailUrl;
	protected $thumbnailWidth;
	protected $thumbnailHeight;

	public function getRecordInfo()
	{
		return new RecordInfo('type', array(
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
		));
	}

	public function setType($type)
	{
		if(!in_array($type, array('link', 'photo', 'rich', 'video')))
		{
			throw new InvalidDataException('Invalid type');
		}

		$this->type = $type;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setVersion($version)
	{
		if($version != '1.0')
		{
			throw new InvalidDataException('Invalid version');
		}

		$this->version = $version;
	}

	public function getVersion()
	{
		return $this->version;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setAuthorName($authorName)
	{
		$this->authorName = $authorName;
	}

	public function getAuthorName()
	{
		return $this->authorName;
	}

	public function setAuthorUrl($authorUrl)
	{
		$this->authorUrl = $authorUrl;
	}

	public function getAuthorUrl()
	{
		return $this->authorUrl;
	}

	public function setProviderName($providerName)
	{
		$this->providerName = $providerName;
	}

	public function getProviderName()
	{
		return $this->providerName;
	}

	public function setProviderUrl($providerUrl)
	{
		$this->providerUrl = $providerUrl;
	}

	public function getProviderUrl()
	{
		return $this->providerUrl;
	}

	public function setCacheAge($cacheAge)
	{
		$this->cacheAge = (int) $cacheAge;
	}

	public function getCacheAge()
	{
		return $this->cacheAge;
	}

	public function setThumbnailUrl($thumbnailUrl)
	{
		$this->thumbnailUrl = $thumbnailUrl;
	}

	public function getThumbnailUrl()
	{
		return $this->thumbnailUrl;
	}

	public function setThumbnailWidth($thumbnailWidth)
	{
		$this->thumbnailWidth = $thumbnailWidth;
	}

	public function getThumbnailWidth()
	{
		return $this->thumbnailWidth;
	}

	public function setThumbnailHeight($thumbnailHeight)
	{
		$this->thumbnailHeight = $thumbnailHeight;
	}

	public function getThumbnailHeight()
	{
		return $this->thumbnailHeight;
	}

	public static function factory($data)
	{
		$type = isset($data['type']) ? strtolower($data['type']) : null;

		if(!in_array($type, array('link', 'photo', 'rich', 'video')))
		{
			throw new InvalidDataException('Invalid type');
		}

		$class = '\PSX\Oembed\Type\\' . ucfirst($type);

		if(class_exists($class))
		{
			$record   = new $class();
			$importer = new DefaultImporter();
			$importer->import($record, $data);

			return $record;
		}
		else
		{
			throw new InvalidDataException('Type class not found');
		}
	}
}
