<?php
/*
 *  $Id: DomTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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
 * PSX_Data_Reader_DomTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class PSX_Data_Reader_DomTest extends PHPUnit_Framework_TestCase
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
<?xml version="1.0" encoding="UTF-8"?>
<foo>
	<bar>jedi</bar>
	<baz>power</baz>
</foo>
INPUT;

		$reader  = new PSX_Data_Reader_Dom();
		$message = new PSX_Http_Message(array(), $body);

		$result = $reader->read($message);
		$dom    = $result->getData();

		foreach($dom->childNodes as $node)
		{
			if($node->nodeType == XML_ELEMENT_NODE)
			{
				$root = $node;
			}
		}

		$this->assertEquals(PSX_Data_ReaderInterface::DOM, $result->getType());
		$this->assertEquals(true, $dom instanceof DomDocument);
		$this->assertEquals('foo', $root->localName);
	}
}

