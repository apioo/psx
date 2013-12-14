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

namespace PSX\Html;

use PSX\Html\Lexer\Token\Element;

/**
 * LexerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class LexerTest extends \PHPUnit_Framework_TestCase
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

		$root = Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testMissingClosingTag()
	{
		$html = <<<HTML
<html>
	<head>
		<title>barfoo</title>
	</head>
	<body>
		<h1>foobar
	</body>
</html>
HTML;

		$expect = <<<HTML
<html>
	<head>
		<title>barfoo</title>
	</head>
	<body>
		<h1>foobar
	
</h1></body></html>
HTML;

		$root = Lexer::parse($html);

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
		<title /></head></html>
HTML;

		$root = Lexer::parse($html);

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

		$root = Lexer::parse($html);

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

		$root = Lexer::parse($html);

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

		$root = Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testScriptTag()
	{
		$html = <<<HTML
<html>
	<head>
		<title>barfoo</title>
		<script><h1>Oo</h1></h1></script>
	</head>
</html>
HTML;

		$expect = <<<HTML
<html>
	<head>
		<title>barfoo</title>
		<script><h1>Oo</h1></h1></script>
	</head>
</html>
HTML;

		$root = Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testDoubleClosedTag()
	{
		$html = <<<HTML
<html>
	<head>
		<title>barfoo</title>
	</head>
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

		$root = Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testSingleElement()
	{
		$html = <<<HTML
<html name="test" foo="bar" />
HTML;

		$root = Lexer::parse($html);

		$this->assertEquals(true, $root instanceof Element);
		$this->assertEquals('html', $root->name);
		$this->assertEquals('test', $root->getAttribute('name'));
		$this->assertEquals('bar', $root->getAttribute('foo'));
	}

	public function testTextString()
	{
		$html = <<<HTML
lorem ipsum
HTML;

		$root = Lexer::parse($html);

		$this->assertEquals(null, $root);
	}

	public function testElementInTextString()
	{
		$html = <<<HTML
lorem <ipsum> foobar
HTML;

		$root = Lexer::parse($html);

		$this->assertEquals(true, $root instanceof Element);
		$this->assertEquals('ipsum', $root->name);
		$this->assertEquals(array(), $root->getAttributes());
	}

	public function testJsonString()
	{
		$html = <<<HTML
{"foo":"bar"}
HTML;

		$root = Lexer::parse($html);

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

		$root = Lexer::parse($html);

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

		$root = Lexer::parse($html);

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

		$root = Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testAttributesContainsLowerSign()
	{
		$html = <<<HTML
<html>
	<body style="foo < bar">
	</body>
</html>
HTML;

		$expect = <<<HTML
<html>
	<body style="foo &lt; bar">
	</body>
</html>
HTML;

		$root = Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testAttributesContainsGreaterSign()
	{
		$html = <<<HTML
<html>
	<body style="foo > bar">
	</body>
</html>
HTML;

		$expect = <<<HTML
<html>
	<body style="foo &gt; bar">
	</body>
</html>
HTML;

		$root = Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testYoutubeIframe()
	{
		$html = <<<HTML
<iframe width="420" height="315" src="http://www.youtube.com/embed/jExym4mLx10?rel=0" frameborder="0" allowfullscreen></iframe>
HTML;

		$expect = <<<HTML
<iframe width="420" height="315" src="http://www.youtube.com/embed/jExym4mLx10?rel=0" frameborder="0" allowfullscreen></iframe>
HTML;

		$root = Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testEmptyAttributes()
	{
		$html = <<<HTML
<p>
	<input readonly type="text" value="foo" />
</p>
HTML;

		$expect = <<<HTML
<p>
	<input readonly type="text" value="foo" />
</p>
HTML;

		$root = Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testScriptContent()
	{
		$html = <<<HTML
<script type="text/javascript">
if (0 < 1 && 1 > 0) {
	alert('Oo');
}
</script>
HTML;

		$expect = <<<HTML
<script type="text/javascript">
if (0 < 1 && 1 > 0) {
	alert('Oo');
}
</script>
HTML;

		$root = Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testHtmlComment()
	{
		$html = <<<HTML
<html>
	<body>
		<p>some foo</p>
		<!--<p> some bar </p>-->
	</body>
</html>
HTML;

		$expect = <<<HTML
<html>
	<body>
		<p>some foo</p>
		<!--<p> some bar </p>-->
	</body>
</html>
HTML;

		$root = Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testTextareaContent()
	{
		$html = <<<HTML
<textarea>
	<a><b></a></b>
	<<php
	>>
</textarea>
HTML;

		$expect = <<<HTML
<textarea>
	<a><b></a></b>
	<<php
	>>
</textarea>
HTML;

		$root = Lexer::parse($html);

		$this->assertEquals($expect, $root->__toString());
	}

	public function testParseAttributes()
	{
		$this->assertEquals(array('disabled' => null), Lexer::parseAttributes('disabled'));
		$this->assertEquals(array('value' => 'yes'), Lexer::parseAttributes('value=yes'));
		$this->assertEquals(array('value' => 'yes'), Lexer::parseAttributes('value =yes'));
		$this->assertEquals(array('type' => 'checkbox'), Lexer::parseAttributes('type=\'checkbox\''));
		$this->assertEquals(array('name' => 'be evil'), Lexer::parseAttributes('name="be evil"'));
		$this->assertEquals(array('name' => 'foo', 'bar' => 'name'), Lexer::parseAttributes('name="foo" bar="name"'));
		$this->assertEquals(array('name' => ' be evil '), Lexer::parseAttributes(' name = " be evil " '));
		$this->assertEquals(array('name' => ' be evil '), Lexer::parseAttributes(' name = \' be evil \' '));
		$this->assertEquals(array('name' => ' be \'evil '), Lexer::parseAttributes(' name = " be \'evil " '));
		$this->assertEquals(array('name' => ' be "evil '), Lexer::parseAttributes(' name = \' be "evil \' '));
		$this->assertEquals(array('itemscope' => 'itemscope', 'itemtype' => 'http://schema.org/WebPage'), Lexer::parseAttributes('itemscope="itemscope" itemtype="http://schema.org/WebPage"'));
		$this->assertEquals(array('itemscope' => 'itemscope', 'itemtype' => 'http://schema.org/WebPage'), Lexer::parseAttributes('        itemscope  =  "itemscope"     itemtype  =  "http://schema.org/WebPage"      '));
		$this->assertEquals(array(
			'bgcolor' => '#ffffff',
			'text' => '#000000',
			'link' => '#0000cc',
			'vlink' => '#551a8b',
			'alink' => '#ff0000',
			'onload' => 'document.f&&document.f.q.focus();document.gbqf&&document.gbqf.q.focus();if(document.images)new Image().src=\'/images/srpr/nav_logo80.png\'',
		), Lexer::parseAttributes('bgcolor=#ffffff text=#000000 link=#0000cc vlink=#551a8b alink=#ff0000 onload="document.f&&document.f.q.focus();document.gbqf&&document.gbqf.q.focus();if(document.images)new Image().src=\'/images/srpr/nav_logo80.png\'"'));
	}
}
