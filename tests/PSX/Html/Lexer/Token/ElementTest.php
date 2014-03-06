<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Html\Lexer\Token;

/**
 * ElementTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ElementTest extends \PHPUnit_Framework_TestCase
{
	public function testParse()
	{
		$element = Element::parse('div class="foo"');

		$this->assertEquals('div', $element->name);
		$this->assertEquals(array('class' => 'foo'), $element->attr);
		$this->assertEquals(false, $element->short);
		$this->assertEquals(Element::TYPE_START, $element->type);

		$element = Element::parse('img src="foo.png" alt="bar" /');

		$this->assertEquals('img', $element->name);
		$this->assertEquals(array('src' => 'foo.png', 'alt' => 'bar'), $element->attr);
		$this->assertEquals(true, $element->short);
		$this->assertEquals(Element::TYPE_START, $element->type);

		$element = Element::parse('br');

		$this->assertEquals('br', $element->name);
		$this->assertEquals(array(), $element->attr);
		$this->assertEquals(false, $element->short);
		$this->assertEquals(Element::TYPE_START, $element->type);

		$element = Element::parse('br /');

		$this->assertEquals('br', $element->name);
		$this->assertEquals(array(), $element->attr);
		$this->assertEquals(true, $element->short);
		$this->assertEquals(Element::TYPE_START, $element->type);

		$element = Element::parse('  br  ');

		$this->assertEquals('br', $element->name);
		$this->assertEquals(array(), $element->attr);
		$this->assertEquals(false, $element->short);
		$this->assertEquals(Element::TYPE_START, $element->type);

		$element = Element::parse('/div');

		$this->assertEquals('div', $element->name);
		$this->assertEquals(array(), $element->attr);
		$this->assertEquals(false, $element->short);
		$this->assertEquals(Element::TYPE_END, $element->type);

	}
}
