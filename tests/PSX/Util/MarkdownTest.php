<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Util;

/**
 * MarkdownTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class MarkdownTest extends \PHPUnit_Framework_TestCase
{
	public function testParagraph()
	{
		$md = <<<TEXT
some content with text
including a new line and so on

and another text
TEXT;

		$actual = Markdown::decode($md);
		$expect = '<p>some content with text including a new line and so on </p>' . "\n";
		$expect.= '<p>and another text </p>' . "\n";

		$this->assertEquals($expect, $actual);
	}

	public function testBr()
	{
		$md = <<<TEXT
some content with text  
including a new line and so on

and another text
TEXT;

		$actual = Markdown::decode($md);
		$expect = '<p>some content with text  <br /> including a new line and so on </p>' . "\n";
		$expect.= '<p>and another text </p>' . "\n";

		$this->assertEquals($expect, $actual);
	}

	public function testQuote()
	{
		$md = <<<TEXT
> this is a test quote
> ## some heading
> another text

and an reply to this quote
TEXT;

		$actual = Markdown::decode($md);
		$expect = '<blockquote>' . "\n";
		$expect.= '<p>this is a test quote </p>' . "\n";
		$expect.= '<h2>some heading</h2>' . "\n";
		$expect.= '<p>another text </p>' . "\n";
		$expect.= '</blockquote>' . "\n";
		$expect.= '<p>and an reply to this quote </p>' . "\n";

		$this->assertEquals($expect, $actual);
	}

	public function testUnorderedList()
	{
		$md = <<<TEXT
some content

* foo
* bar
TEXT;

		$actual = Markdown::decode($md);
		$expect = '<p>some content </p>' . "\n";
		$expect.= '<ul>' . "\n";
		$expect.= '	<li>foo</li>' . "\n";
		$expect.= '	<li>bar</li>' . "\n";
		$expect.= '</ul>' . "\n";

		$this->assertEquals($expect, $actual);
	}

	public function testOrderedList()
	{
		$md = <<<TEXT
some content

1. foo
2. bar
TEXT;

		$actual = Markdown::decode($md);
		$expect = '<p>some content </p>' . "\n";
		$expect.= '<ol>' . "\n";
		$expect.= '	<li>foo</li>' . "\n";
		$expect.= '	<li>bar</li>' . "\n";
		$expect.= '</ol>' . "\n";

		$this->assertEquals($expect, $actual);
	}

	public function testHeading()
	{
		$md = <<<TEXT
# foobar
## foobar
### foobar
#### foobar
##### foobar
###### foobar
TEXT;

		$actual = Markdown::decode($md);
		$expect = '<h1>foobar</h1>' . "\n";
		$expect.= '<h2>foobar</h2>' . "\n";
		$expect.= '<h3>foobar</h3>' . "\n";
		$expect.= '<h4>foobar</h4>' . "\n";
		$expect.= '<h5>foobar</h5>' . "\n";
		$expect.= '<h6>foobar</h6>' . "\n";

		$this->assertEquals($expect, $actual);
	}

	public function testLine()
	{
		$md = <<<TEXT
some content
--
some other content
TEXT;

		$actual = Markdown::decode($md);
		$expect = '<p>some content </p>' . "\n";
		$expect.= '<hr />' . "\n";
		$expect.= '<p>some other content </p>' . "\n";

		$this->assertEquals($expect, $actual);
	}

	public function testCode()
	{
		$md = <<<TEXT
some code description

	class Voila {
	public:
	  // Voila
	  static const string VOILA = "Voila";
	  // will not interfere with embedded tags.
	}
TEXT;

		$actual = Markdown::decode($md);
		$expect = '<p>some code description </p>' . "\n";
		$expect.= '<pre class="prettyprint">class Voila {' . "\n";
		$expect.= 'public:' . "\n";
		$expect.= '  // Voila' . "\n";
		$expect.= '  static const string VOILA = "Voila";' . "\n";
		$expect.= '  // will not interfere with embedded tags.' . "\n";
		$expect.= '}' . "\n";
		$expect.= '</pre>' . "\n";

		$this->assertEquals($expect, $actual);
	}

	public function testEmphasisItalic()
	{
		$md = <<<TEXT
foo _bar_ foo *bar* foo *bar* foo _bar_ f*oo bar
TEXT;

		$actual = Markdown::decode($md);
		$expect = '<p>foo <i>bar</i> foo <i>bar</i> foo <i>bar</i> foo <i>bar</i> f*oo bar </p>' . "\n";

		$this->assertEquals($expect, $actual);
	}

	public function testEmphasisItalicUrl()
	{
		$md = <<<TEXT
foo _bar_ foo http://foo.com/test_test_test.htm
TEXT;

		$actual = Markdown::decode($md);
		$expect = '<p>foo <i>bar</i> foo http://foo.com/test_test_test.htm </p>' . "\n";

		$this->assertEquals($expect, $actual);
	}

	public function testEmphasisBold()
	{
		$md = <<<TEXT
foo __bar__ foo **bar** foo **bar** foo __bar__ f**oo bar
TEXT;

		$actual = Markdown::decode($md);
		$expect = '<p>foo <b>bar</b> foo <b>bar</b> foo <b>bar</b> foo <b>bar</b> f**oo bar </p>' . "\n";

		$this->assertEquals($expect, $actual);
	}

	public function testEmphasisBoldUrl()
	{
		$md = <<<TEXT
foo __bar__ foo http://foo.com/test__test__test.htm
TEXT;

		$actual = Markdown::decode($md);
		$expect = '<p>foo <b>bar</b> foo http://foo.com/test__test__test.htm </p>' . "\n";

		$this->assertEquals($expect, $actual);
	}

	public function testAlreadyEncoded()
	{
		$md = <<<TEXT
<p>foo</p>
TEXT;

		$actual = Markdown::decode($md);
		$expect = '<p>foo</p>';

		$this->assertEquals($expect, $actual);
	}

	public function testCodeWithWhitespaceLine()
	{
		$md = <<<TEXT
some code description

	class Voila {
  
  
	public:
	  // Voila
	  static const string VOILA = "Voila";
	  // will not interfere with embedded tags.
	}
TEXT;

		$actual = Markdown::decode($md);
		$expect = '<p>some code description </p>' . "\n";
		$expect.= '<pre class="prettyprint">class Voila {' . "\n";
		$expect.= '  ' . "\n";
		$expect.= '  ' . "\n";
		$expect.= 'public:' . "\n";
		$expect.= '  // Voila' . "\n";
		$expect.= '  static const string VOILA = "Voila";' . "\n";
		$expect.= '  // will not interfere with embedded tags.' . "\n";
		$expect.= '}' . "\n";
		$expect.= '</pre>' . "\n";

		$this->assertEquals($expect, $actual);
	}

	public function testNestedQuote()
	{
		$md = <<<TEXT
> this is a test quote
> > ## some heading
> > another text

and an reply to this quote
TEXT;

		$actual = Markdown::decode($md);
		$expect = '<blockquote>' . "\n";
		$expect.= '<p>this is a test quote </p>' . "\n";
		$expect.= '<h2>some heading</h2>' . "\n";
		$expect.= '<p>another text </p>' . "\n";
		$expect.= '</blockquote>' . "\n";
		$expect.= '<p>and an reply to this quote </p>' . "\n";

		$this->assertEquals($expect, $actual);
	}
}
