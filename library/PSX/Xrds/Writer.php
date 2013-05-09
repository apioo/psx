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

namespace PSX\Xrds;

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
	public static $mime  = 'application/xrds+xml';
	public static $xmlns = 'xri://$xrd*($v*2.0)';

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

		$this->writer->startElementNs('xrds', 'XRDS', 'xri://$xrds');
		$this->writer->writeAttribute('xmlns', self::$xmlns);
		$this->writer->startElement('XRD');
	}

	public function addService($uri, array $types = array(), $priority = null)
	{
		$this->writer->startElement('Service');

		if($priority !== null)
		{
			$this->writer->writeAttribute('priority', $priority);
		}

		foreach($types as $type)
		{
			$this->writer->writeElement('Type', $type);
		}

		$this->writer->writeElement('URI', $uri);
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
		$this->writer->endDocument();

		return $this->writer->outputMemory();	
	}

	public function getWriter()
	{
		return $this->writer;
	}
}
