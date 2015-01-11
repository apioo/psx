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

namespace PSX\Data\Writer;

use PSX\Data\ExceptionRecord;
use PSX\Data\RecordInterface;
use PSX\Data\WriterInterface;
use PSX\Http\MediaType;
use PSX\Xml\Writer;
use XMLWriter;

/**
 * Soap
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Soap extends Xml
{
	public static $mime = 'application/soap+xml';

	protected $namespace;
	protected $requestMethod;

	public function __construct($namespace)
	{
		$this->namespace = $namespace;
	}

	public function setRequestMethod($requestMethod)
	{
		$this->requestMethod = strtolower($requestMethod);
	}

	public function getRequestMethod()
	{
		return $this->requestMethod;
	}

	public function write(RecordInterface $record)
	{
		$xmlWriter = new XMLWriter();
		$xmlWriter->openMemory();
		$xmlWriter->setIndent(true);
		$xmlWriter->startDocument('1.0', 'UTF-8');

		$xmlWriter->startElement('soap:Envelope');
		$xmlWriter->writeAttribute('xmlns:soap', 'http://schemas.xmlsoap.org/soap/envelope/');

		if($record instanceof ExceptionRecord)
		{
			$xmlWriter->startElement('soap:Body');
			$xmlWriter->startElement('soap:Fault');

			$xmlWriter->writeElement('faultcode', 'soap:Server');
			$xmlWriter->writeElement('faultstring', $record->getMessage());

			if($record->getTrace())
			{
				$xmlWriter->startElement('detail');

				$writer = new Writer($xmlWriter);
				$writer->setRecord($record->getRecordInfo()->getName(), $this->export($record), $this->namespace);

				$xmlWriter->endElement();
			}

			$xmlWriter->endElement();
			$xmlWriter->endElement();
		}
		else
		{
			$xmlWriter->startElement('soap:Body');

			$writer = new Writer($xmlWriter);
			$writer->setRecord($this->requestMethod . 'Response', $this->export($record), $this->namespace);

			$xmlWriter->endElement();
		}

		$xmlWriter->endElement();

		return $writer->toString();
	}

	public function isContentTypeSupported(MediaType $contentType)
	{
		return $contentType->getName() == self::$mime;
	}

	public function getContentType()
	{
		return 'text/xml';
	}
}
