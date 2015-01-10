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

namespace PSX\Rss;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Source
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Source extends RecordAbstract
{
	protected $text;
	protected $url;

	public function __construct($text = null, $url = null)
	{
		if($text !== null)
		{
			$this->setText($text);
		}

		if($url !== null)
		{
			$this->setUrl($url);
		}
	}

	public function getRecordInfo()
	{
		return new RecordInfo('source', array(
			'text' => $this->text,
			'url'  => $this->url,
		));
	}

	public function setText($text)
	{
		$this->text = $text;
	}

	public function getText()
	{
		return $this->text;
	}

	public function setUrl($url)
	{
		$this->url = $url;
	}

	public function getUrl()
	{
		return $this->url;
	}

	public function __toString()
	{
		return $this->text;
	}
}
