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

namespace PSX\Data\Record\Visitor;

use PSX\Data\RecordInterface;
use PSX\Data\Record\VisitorAbstract;
use RuntimeException;
use XMLWriter;

/**
 * XmlWriterVisitor
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class XmlWriterVisitor extends VisitorAbstract
{
	protected $writer;
	protected $namespace;

	protected $objectKey;
	protected $level      = 0;
	protected $arrayStart = false;
	protected $arrayEnd   = false;

	public function __construct(XMLWriter $writer, $namespace = null)
	{
		$this->writer    = $writer;
		$this->namespace = $namespace;
	}

	public function visitObjectStart($name)
	{
		if($this->level == 0)
		{
			$this->writer->startElement($name);

			if($this->namespace !== null)
			{
				$this->writer->writeAttribute('xmlns', $this->namespace);
			}
		}

		$this->level++;
	}

	public function visitObjectEnd()
	{
		$this->level--;

		if($this->level == 0)
		{
			$this->writer->endElement();
		}
	}

	public function visitObjectValueStart($key, $value)
	{
		$this->writer->startElement($this->objectKey = $key);
	}

	public function visitObjectValueEnd()
	{
		if(!$this->arrayEnd)
		{
			$this->writer->endElement();
		}

		$this->arrayEnd = false;
	}

	public function visitArrayStart($array)
	{
		$this->arrayStart = true;
		$this->arrayKey   = $this->objectKey;
	}

	public function visitArrayEnd()
	{
		$this->arrayEnd = true;
		$this->arrayKey = null;
	}

	public function visitArrayValueStart($value)
	{
		if(!$this->arrayStart)
		{
			$this->writer->startElement($this->arrayKey);
		}

		$this->arrayStart = false;
	}

	public function visitArrayValueEnd()
	{
		$this->writer->endElement();
	}

	public function visitValue($value)
	{
		$this->writer->text($this->getValue($value));
	}

	protected function getValue($value)
	{
		if($value instanceof \DateTime)
		{
			return $value->format(\DateTime::ATOM);
		}
		else if(is_bool($value))
		{
			return $value ? 'true' : 'false';
		}
		else
		{
			return (string) $value;
		}
	}
}
