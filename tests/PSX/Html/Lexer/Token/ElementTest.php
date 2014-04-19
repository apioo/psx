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

use PSX\Html\Lexer\TokenAbstract;

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

		$this->assertEquals('div', $element->getName());
		$this->assertEquals(array('class' => 'foo'), $element->getAttributes());
		$this->assertEquals(false, $element->isShort());
		$this->assertEquals(TokenAbstract::TYPE_ELEMENT_START, $element->getType());

		$element = Element::parse('img src="foo.png" alt="bar" /');

		$this->assertEquals('img', $element->getName());
		$this->assertEquals(array('src' => 'foo.png', 'alt' => 'bar'), $element->getAttributes());
		$this->assertEquals(true, $element->isShort());
		$this->assertEquals(TokenAbstract::TYPE_ELEMENT_START, $element->getType());

		$element = Element::parse('br');

		$this->assertEquals('br', $element->getName());
		$this->assertEquals(array(), $element->getAttributes());
		$this->assertEquals(false, $element->isShort());
		$this->assertEquals(TokenAbstract::TYPE_ELEMENT_START, $element->getType());

		$element = Element::parse('br /');

		$this->assertEquals('br', $element->getName());
		$this->assertEquals(array(), $element->getAttributes());
		$this->assertEquals(true, $element->isShort());
		$this->assertEquals(TokenAbstract::TYPE_ELEMENT_START, $element->getType());

		$element = Element::parse('  br  ');

		$this->assertEquals('br', $element->getName());
		$this->assertEquals(array(), $element->getAttributes());
		$this->assertEquals(false, $element->isShort());
		$this->assertEquals(TokenAbstract::TYPE_ELEMENT_START, $element->getType());

		$element = Element::parse('/div');

		$this->assertEquals('div', $element->getName());
		$this->assertEquals(array(), $element->getAttributes());
		$this->assertEquals(false, $element->isShort());
		$this->assertEquals(TokenAbstract::TYPE_ELEMENT_END, $element->getType());

	}
}
