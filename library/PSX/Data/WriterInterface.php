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

namespace PSX\Data;

use PSX\Http\MediaType;

/**
 * WriterInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface WriterInterface
{
	const ATOM  = 'PSX\Data\Writer\Atom';
	const FORM  = 'PSX\Data\Writer\Form';
	const HTML  = 'PSX\Data\Writer\Html';
	const JSON  = 'PSX\Data\Writer\Json';
	const JSONP = 'PSX\Data\Writer\Jsonp';
	const JSONX = 'PSX\Data\Writer\Jsonx';
	const RSS   = 'PSX\Data\Writer\Rss';
	const SOAP  = 'PSX\Data\Writer\Soap';
	const SVG   = 'PSX\Data\Writer\Svg';
	const TEXT  = 'PSX\Data\Writer\Text';
	const XML   = 'PSX\Data\Writer\Xml';

	/**
	 * Returns the string representation of this record from the writer
	 *
	 * @param \PSX\Data\RecordInterface
	 * @return string
	 */
	public function write(RecordInterface $record);

	/**
	 * Returns whether the content type is supported by this writer
	 *
	 * @param \PSX\Http\MediaType $contentType
	 * @return boolean
	 */
	public function isContentTypeSupported(MediaType $contentType);

	/**
	 * Returns the content type of this writer
	 *
	 * @return string
	 */
	public function getContentType();
}
