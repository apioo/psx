<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Xml;

use XMLWriter;
use PSX\Util\CurveArray;

/**
 * Writer
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
