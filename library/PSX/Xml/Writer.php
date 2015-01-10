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

namespace PSX\Xml;

use XMLWriter;
use PSX\Util\CurveArray;

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
	}

	public function setRecord($name, array $fields, $ns = null)
	{
		$this->recXmlEncode($name, $fields, $ns);
	}

	public function close()
	{
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

	protected function recXmlEncode($name, array $fields, $ns = null)
	{
		if(CurveArray::isAssoc($fields))
		{
			$this->writer->startElement($name);

			if($ns !== null)
			{
				$this->writer->writeAttribute('xmlns', $ns);
			}

			foreach($fields as $k => $v)
			{
				if(is_array($v))
				{
					$this->recXmlEncode($k, $v);
				}
				else if(is_bool($v))
				{
					$this->writer->writeElement($k, $v ? 'true' : 'false');
				}
				else
				{
					$this->writer->writeElement($k, $v);
				}
			}

			$this->writer->endElement();
		}
		else
		{
			foreach($fields as $k => $v)
			{
				if(is_array($v))
				{
					$this->recXmlEncode($name, $v);
				}
				else if(is_bool($v))
				{
					$this->writer->writeElement($name, $v ? 'true' : 'false');
				}
				else
				{
					$this->writer->writeElement($name, $v);
				}
			}
		}
	}
}
