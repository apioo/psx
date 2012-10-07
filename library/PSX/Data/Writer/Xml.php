<?php
/*
 *  $Id: Xml.php 453 2012-03-12 21:10:02Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2009 Christoph Kappestein <k42b3.x@gmail.com>
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
 * PSX_Data_Writer_Xml
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Data
 * @version    $Revision: 453 $
 */
class PSX_Data_Writer_Xml implements PSX_Data_WriterInterface
{
	public static $mime = 'application/xml';

	public $writer;
	public $writerResult;

	public function __construct()
	{
		$this->writer = new XMLWriter();

		$this->writer->openMemory();

		$this->writer->setIndent(true);

		$this->writer->startDocument('1.0', 'UTF-8');
	}

	public function write(PSX_Data_RecordInterface $record)
	{
		$this->writerResult = new PSX_Data_WriterResult(PSX_Data_WriterInterface::XML, $this);

		$this->recXmlEncode($record->getName(), $record->export($this->writerResult));

		$this->writer->endDocument();

		echo $this->writer->outputMemory();
	}

	protected function recXmlEncode($name, array $fields, $ns = null)
	{
		if($this->isAssocArray($fields))
		{
			$this->writer->startElement($name);

			if($ns !== null)
			{
				$this->writer->writeAttribute('xmlns', self::$xmlns);
			}

			foreach($fields as $k => $v)
			{
				if($v instanceof PSX_Data_RecordInterface)
				{
					$this->recXmlEncode($k, $v->export($this->writerResult));
				}
				else if(is_array($v))
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
				if($v instanceof PSX_Data_RecordInterface)
				{
					$this->recXmlEncode($name, $v->export($this->writerResult));
				}
				else if(is_array($v))
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

	private function isAssocArray(array $array)
	{
		$len = count($array);

		for($i = 0; $i < $len; $i++)
		{
			if(!isset($array[$i]))
			{
				return true;
			}
		}

		return false;
	}
}
