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

/**
 * Contract to all xml producing writer classes. The classes are designed to 
 * used either independently producing an complete xml document or used in an
 * context producing only an xml fragment. An writer has add* or set* methods 
 * which write an element
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface WriterInterface
{
	/**
	 * Closes all open element levels
	 *
	 * @return void
	 */
	public function close();

	/**
	 * Returns the xml as string. In most cases this method calls the close()
	 * method, ends the document and returns the result as string
	 *
	 * @return string
	 */
	public function toString();

	/**
	 * Returns the underlying xml writer
	 *
	 * @return XMLWriter
	 */
	public function getWriter();
}
