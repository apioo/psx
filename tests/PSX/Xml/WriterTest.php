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

namespace PSX\Xml;

/**
 * WriterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class WriterTest extends \PHPUnit_Framework_TestCase
{
	public function testWriter()
	{
		$writer = new Writer();
		$writer->setRecord('foo', array(
			'foo1' => 'bar',
			'foo2' => array(
				'bar1' => 'foo', 
				'bar2' => 'foo',
			),
			'foo3' => 'bar',
			'foo4' => 'bar',
			'foo5' => 'bar',
		));

		$actual   = $writer->toString();
		$expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<foo>
  <foo1>bar</foo1>
  <foo2>
    <bar1>foo</bar1>
    <bar2>foo</bar2>
  </foo2>
  <foo3>bar</foo3>
  <foo4>bar</foo4>
  <foo5>bar</foo5>
</foo>
XML;

		$this->assertXmlStringEqualsXmlString($expected, $actual);
	}
}
