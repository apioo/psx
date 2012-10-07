<?php
/*
 *  $Id: MarkdownTest.php 649 2012-10-06 22:15:23Z k42b3.x@googlemail.com $
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
 * PSX_Util_MarkdownTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 649 $
 */
class PSX_Util_MarkdownTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testParagraph()
	{
		$md = <<<TEXT
some content with text
including a new line and so on

and another text
TEXT;

		$a = PSX_Util_Markdown::decode($md);
		$e = <<<TEXT
<p>some content with text including a new line and so on </p>
<p>and another text </p>

TEXT;

		$this->assertEquals($e, $a);
	}

	public function testBr()
	{
		$md = <<<TEXT
some content with text  
including a new line and so on

and another text
TEXT;

		$a = PSX_Util_Markdown::decode($md);
		$e = <<<TEXT
<p>some content with text  <br /> including a new line and so on </p>
<p>and another text </p>

TEXT;

		$this->assertEquals($e, $a);
	}

	public function testQuote()
	{
		$md = <<<TEXT
> this is a test quote
> ## some heading
> another text

and an reply to this quote
TEXT;

		$a = PSX_Util_Markdown::decode($md);
		$e = <<<TEXT
<blockquote>
<p>this is a test quote </p>
<h2>some heading</h2>
<p>another text </p>
</blockquote>
<p>and an reply to this quote </p>

TEXT;

		$this->assertEquals($e, $a);
	}

	public function testUnorderedList()
	{
		$md = <<<TEXT
some content

* foo
* bar
TEXT;

		$a = PSX_Util_Markdown::decode($md);
		$e = <<<TEXT
<p>some content </p>
<ul>
	<li>foo</li>
	<li>bar</li>
</ul>

TEXT;

		$this->assertEquals($e, $a);
	}

	public function testOrderedList()
	{
		$md = <<<TEXT
some content

1. foo
2. bar
TEXT;

		$a = PSX_Util_Markdown::decode($md);
		$e = <<<TEXT
<p>some content </p>
<ol>
	<li>foo</li>
	<li>bar</li>
</ol>

TEXT;

		$this->assertEquals($e, $a);
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

		$a = PSX_Util_Markdown::decode($md);
		$e = <<<TEXT
<h1>foobar</h1>
<h2>foobar</h2>
<h3>foobar</h3>
<h4>foobar</h4>
<h5>foobar</h5>
<h6>foobar</h6>

TEXT;

		$this->assertEquals($e, $a);
	}

	public function testLine()
	{
		$md = <<<TEXT
some content
--
some other content
TEXT;

		$a = PSX_Util_Markdown::decode($md);
		$e = <<<TEXT
<p>some content </p>
<hr />
<p>some other content </p>

TEXT;

		$this->assertEquals($e, $a);
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

		$a = PSX_Util_Markdown::decode($md);
		$e = <<<TEXT
<p>some code description </p>
<pre class="prettyprint">class Voila {
public:
  // Voila
  static const string VOILA = "Voila";
  // will not interfere with embedded tags.
}
</pre>

TEXT;

		$this->assertEquals($e, $a);
	}

	public function testEmphasisItalic()
	{
		$md = <<<TEXT
foo _bar_ foo *bar* foo *bar* foo _bar_ f*oo bar
TEXT;

		$a = PSX_Util_Markdown::decode($md);
		$e = <<<TEXT
<p>foo <i>bar</i> foo <i>bar</i> foo <i>bar</i> foo <i>bar</i> f*oo bar </p>

TEXT;

		$this->assertEquals($e, $a);
	}

	public function testEmphasisBold()
	{
		$md = <<<TEXT
foo __bar__ foo **bar** foo **bar** foo __bar__ f**oo bar
TEXT;

		$a = PSX_Util_Markdown::decode($md);
		$e = <<<TEXT
<p>foo <b>bar</b> foo <b>bar</b> foo <b>bar</b> foo <b>bar</b> f**oo bar </p>

TEXT;

		$this->assertEquals($e, $a);
	}
}
