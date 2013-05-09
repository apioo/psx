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

namespace PSX\Sitemap;

use DateTime;
use PSX\Exception;
use PSX\ModuleAbstract;
use PSX\Url;
use PSX\Xml\WriterInterface;
use XMLWriter;

/**
 * Writer
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Writer implements WriterInterface
{
	public static $mime  = 'application/xml';
	public static $xmlns = 'http://www.sitemaps.org/schemas/sitemap/0.9';

	public static $freq  = array('always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never');

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

		$this->writer->startElement('urlset');
		$this->writer->writeAttribute('xmlns', self::$xmlns);
	}

	public function addUrl(Url $loc, DateTime $lastmod = null, $changefreq = null, $priority = null)
	{
		// check length
		$url = $loc->getUrl();

		if(strlen($url) >= 2048)
		{
			throw new Exception('Location value must be less than 2048 characters');
		}

		$this->writer->startElement('url');
		$this->writer->writeElement('loc', $url);

		if($lastmod !== null)
		{
			$this->writer->writeElement('lastmod', $lastmod->format(DateTime::W3C));
		}

		if($changefreq !== null)
		{
			if(in_array($changefreq, self::$freq))
			{
				$this->writer->writeElement('changefreq', $changefreq);
			}
			else
			{
				throw new Exception('Invalid change frequence must be one of ' . implode(', ', self::$freq));
			}
		}

		if($priority !== null)
		{
			$priority = (float) $priority;

			if($priority >= 0.0 && $priority <= 1.0)
			{
				$this->writer->writeElement('priority', $priority);
			}
			else
			{
				throw new Exception('Invalid priority must be between 0.0 and 0.1');
			}
		}

		$this->writer->endElement();		
	}

	public function close()
	{
		$this->writer->endElement();
	}

	public function output()
	{
		header('Content-Type: ' . self::$mime);

		echo $this->toString();
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

	public static function link($title, $href)
	{
		return '<link rel="search" type="' . self::$mime . '" title="' . $title . '" href="' . $href . '" />';
	}
}
