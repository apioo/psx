<?php
/*
 *  $Id: XmlTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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
 * PSX_Data_Reader_XmlTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class PSX_Data_Reader_XmlTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testRead()
	{
		$body = <<<INPUT
<result>
	<row>
		<title>foo</title>
		<user_id>1</user_id>
		<date>1301507663</date>
	</row>
	<row>
		<title>bar</title>
		<user_id>2</user_id>
		<date>1301507667</date>
	</row>
</result>
INPUT;

		$reader  = new PSX_Data_Reader_Xml();
		$message = new PSX_Http_Message(array(), $body);

		$result = $reader->read($message);
		$xml    = $result->getData();

		$e = new PSX_Xml($body);

		$this->assertEquals(PSX_Data_ReaderInterface::XML, $result->getType());
		$this->assertEquals(true, $xml instanceof SimpleXMLElement);
		$this->assertEquals($e, $xml);
	}
}