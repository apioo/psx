<?php
/*
 *  $Id: LexerTest.php 544 2012-07-10 22:28:16Z k42b3.x@googlemail.com $
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
 * PSX_Html_LexerTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 544 $
 */
class PSX_Html_LexerTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testNormalHtml()
	{
		$html = <<<HTML
<html>
	<head>
		<title>barfoo</title>
	</head>
	<body>
		<h1>foobar</h1>
	</body>
</html>
HTML;

		$expect = <<<HTML
<html>
	<head>
		<title>barfoo</title>
	</head>
	<body>
		<h1>foobar</h1>
	</body>
</html>
HTML;

		$root = PSX_Html_Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testMissingClosingTag()
	{
		$html = <<<HTML
<html>
	<head>
		<title>barfoo
	</head>
	<body>
		<h1>foobar</h1>
	</body>
</html>
HTML;

		$expect = <<<HTML
<html>
	<head>
		<title>barfoo
	</title>
	<body>
		<h1>foobar</h1>
	</body>
</head></html>
HTML;

		$root = PSX_Html_Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testCrappyTags()
	{
		$html = <<<HTML
<html>
	<head>
		<title   <title>barfoo
	</head>
	<body>
		<h1>foobar</h1>
	/body>
</html>
HTML;

		$expect = <<<HTML
<html>
	<head>
		<title>barfoo
	</title>
	<body>
		<h1>foobar</h1>
</body></head></html>
HTML;

		$root = PSX_Html_Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testShortTags()
	{
		$html = <<<HTML
<html>
	<head>
		<title>barfoo</title>
		<meta />
	</head>
	<body>
		<h1>foobar</h1>
	</body>
</html>
HTML;

		$expect = <<<HTML
<html>
	<head>
		<title>barfoo</title>
		<meta />
	</head>
	<body>
		<h1>foobar</h1>
	</body>
</html>
HTML;

		$root = PSX_Html_Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testShortTagsDirect()
	{
		$html = <<<HTML
<html>
	<head>
		<title>barfoo</title>
		<meta/>
	</head>
	<body>
		<h1>foobar</h1>
	</body>
</html>
HTML;

		$expect = <<<HTML
<html>
	<head>
		<title>barfoo</title>
		<meta />
	</head>
	<body>
		<h1>foobar</h1>
	</body>
</html>
HTML;

		$root = PSX_Html_Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testShortTagsClosed()
	{
		$html = <<<HTML
<html>
	<head>
		<title>barfoo</title>
		<meta></meta>
	</head>
	<body>
		<h1>foobar</h1>
	</body>
</html>
HTML;

		$expect = <<<HTML
<html>
	<head>
		<title>barfoo</title>
		<meta />
	</head>
	<body>
		<h1>foobar</h1>
	</body>
</html>
HTML;

		$root = PSX_Html_Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testScriptTag()
	{
		$html = <<<HTML
<html>
	<head>
		<title>barfoo</title>
		<script><h1>Oo</h1></script>
	</head>
</html>
HTML;

		$expect = <<<HTML
<html>
	<head>
		<title>barfoo</title>
		<script><h1>Oo</h1></script>
	</head>
</html>
HTML;

		$root = PSX_Html_Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testSingleElement()
	{
		$html = <<<HTML
<html name="test" foo="bar" />
HTML;

		$root = PSX_Html_Lexer::parse($html);

		$this->assertEquals(true, $root instanceof PSX_Html_Lexer_Token_Element);
		$this->assertEquals('html', $root->name);
		$this->assertEquals('test', $root->getAttribute('name'));
		$this->assertEquals('bar', $root->getAttribute('foo'));
	}

	public function testTextString()
	{
		$html = <<<HTML
lorem ipsum
HTML;

		$root = PSX_Html_Lexer::parse($html);

		$this->assertEquals(null, $root);
	}

	public function testElementInTextString()
	{
		$html = <<<HTML
lorem <ipsum> foobar
HTML;

		$root = PSX_Html_Lexer::parse($html);

		$this->assertEquals(true, $root instanceof PSX_Html_Lexer_Token_Element);
		$this->assertEquals('ipsum', $root->name);
		$this->assertEquals(array(), $root->getAttributes());
	}

	public function testJsonString()
	{
		$html = <<<HTML
{"foo":"bar"}
HTML;

		$root = PSX_Html_Lexer::parse($html);

		$this->assertEquals(null, $root);
	}

	public function testAttributeDoubleQuotes()
	{
		$html = <<<HTML
<html>
	<body style="test">
	</body>
</html>
HTML;

		$expect = <<<HTML
<html>
	<body style="test">
	</body>
</html>
HTML;

		$root = PSX_Html_Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testAttributesSingleQuote()
	{
		$html = <<<HTML
<html>
	<body style='test'>
	</body>
</html>
HTML;

		$expect = <<<HTML
<html>
	<body style="test">
	</body>
</html>
HTML;

		$root = PSX_Html_Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testAttributesNoQuote()
	{
		$html = <<<HTML
<html>
	<body style=test>
	</body>
</html>
HTML;

		$expect = <<<HTML
<html>
	<body style="test">
	</body>
</html>
HTML;

		$root = PSX_Html_Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testAttributesContainsGreaterSign()
	{
		$html = <<<HTML
<html>
	<body style="foo < bar">
	</body>
</html>
HTML;

		$expect = <<<HTML
<html>
	<body style="foo < bar">
	</body>
</html>
HTML;

		$root = PSX_Html_Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testParseAttributes()
	{
		$this->assertEquals(array('disabled' => null), PSX_Html_Lexer::parseAttributes('disabled'));
		$this->assertEquals(array('value' => 'yes'), PSX_Html_Lexer::parseAttributes('value=yes'));
		$this->assertEquals(array('value' => 'yes'), PSX_Html_Lexer::parseAttributes('value =yes'));
		$this->assertEquals(array('type' => 'checkbox'), PSX_Html_Lexer::parseAttributes('type=\'checkbox\''));
		$this->assertEquals(array('name' => 'be evil'), PSX_Html_Lexer::parseAttributes('name="be evil"'));
		$this->assertEquals(array('name' => 'foo', 'bar' => 'name'), PSX_Html_Lexer::parseAttributes('name="foo" bar="name"'));
		$this->assertEquals(array('name' => ' be evil '), PSX_Html_Lexer::parseAttributes(' name = " be evil " '));
		$this->assertEquals(array('itemscope' => 'itemscope', 'itemtype' => 'http://schema.org/WebPage'), PSX_Html_Lexer::parseAttributes('itemscope="itemscope" itemtype="http://schema.org/WebPage"'));
		$this->assertEquals(array('itemscope' => 'itemscope', 'itemtype' => 'http://schema.org/WebPage'), PSX_Html_Lexer::parseAttributes('        itemscope  =  "itemscope"     itemtype  =  "http://schema.org/WebPage"      '));
		$this->assertEquals(array(
			'bgcolor' => '#ffffff',
			'text' => '#000000',
			'link' => '#0000cc',
			'vlink' => '#551a8b',
			'alink' => '#ff0000',
			'onload' => 'document.f&&document.f.q.focus();document.gbqf&&document.gbqf.q.focus();if(document.images)new Image().src=\'/images/srpr/nav_logo80.png\'',
		), PSX_Html_Lexer::parseAttributes('bgcolor=#ffffff text=#000000 link=#0000cc vlink=#551a8b alink=#ff0000 onload="document.f&&document.f.q.focus();document.gbqf&&document.gbqf.q.focus();if(document.images)new Image().src=\'/images/srpr/nav_logo80.png\'"'));
	}
}
