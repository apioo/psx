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

namespace PSX\Html\Lexer;

/**
 * DomTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DomTest extends \PHPUnit_Framework_TestCase
{
	public function testPush()
	{
		$dom = new Dom();

		$dom->push(Token\Element::parse('html'));
		$dom->push(Token\Element::parse('head'));
		$dom->push(Token\Element::parse('meta foo="bar"'));
		$dom->push(Token\Element::parse('/head'));
		$dom->push(Token\Element::parse('body'));
		$dom->push(Token\Comment::parse('<!-- foobar -->'));
		$dom->push(Token\Element::parse('div class="Oo"'));
		$dom->push(Token\Text::parse('foobar'));
		$dom->push(Token\Element::parse('/div'));
		$dom->push(Token\Element::parse('/body'));
		$dom->push(Token\Element::parse('/html'));

		$expect = '<html><head><meta foo="bar" /></head><body><!-- foobar --><div class="Oo">foobar</div></body></html>';

		$this->assertEquals($expect, $dom->getRootElement()->__toString());
	}

	public function testPushWhitespaces()
	{
		$dom = new Dom();

		$dom->push(Token\Element::parse('html'));
		$dom->push(Token\Text::parse(PHP_EOL . "\t"));
		$dom->push(Token\Element::parse('head'));
		$dom->push(Token\Text::parse(PHP_EOL . "\t"));
		$dom->push(Token\Element::parse('meta foo="bar"'));
		$dom->push(Token\Text::parse(PHP_EOL . "\t"));
		$dom->push(Token\Element::parse('/head'));
		$dom->push(Token\Text::parse(PHP_EOL . "\t"));
		$dom->push(Token\Element::parse('body'));
		$dom->push(Token\Text::parse(PHP_EOL . "\t"));
		$dom->push(Token\Comment::parse('<!-- foobar -->'));
		$dom->push(Token\Text::parse(PHP_EOL . "\t"));
		$dom->push(Token\Element::parse('div class="Oo"'));
		$dom->push(Token\Text::parse('foobar'));
		$dom->push(Token\Element::parse('/div'));
		$dom->push(Token\Text::parse(PHP_EOL . "\t"));
		$dom->push(Token\Element::parse('/body'));
		$dom->push(Token\Text::parse(PHP_EOL));
		$dom->push(Token\Element::parse('/html'));

		$actual = (string) $dom->getRootElement();
		$expect = <<<HTML
<html>
	<head>
	<meta foo="bar" />
	</head>
	<body>
	<!-- foobar -->
	<div class="Oo">foobar</div>
	</body>
</html>
HTML;

		$this->assertEquals($expect, $actual);
	}
}
