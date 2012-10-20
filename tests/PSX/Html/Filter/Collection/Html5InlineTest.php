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
 * PSX_Html_Filter_Collection_Html5InlineTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 544 $
 */
class PSX_Html_Filter_Collection_Html5InlineTest extends PHPUnit_Framework_TestCase
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
<p>foobar</p>
<ul>
	<li>lorem ipsum</li>
	<li>lorem </li>
</ul>
HTML;

		$expected = <<<HTML
<p>foobar</p>
<ul>
	<li>lorem ipsum</li>
	<li>lorem </li>
</ul>
HTML;

		$filter = new PSX_Html_Filter($html, new PSX_Html_Filter_Collection_Html5Inline());

		$this->assertEquals($expected, $filter->filter());
	}

	public function testInlineJavascript()
	{
		$html = <<<HTML
<div id="fadeOut" style="display:none;">some content</div>
<script type="text/javascript">
$(document).ready(function(){
	$('#fadeOut').fadeIn();
});
</script>
HTML;

		$expected = <<<HTML
<div id="fadeOut" style="display:none;">some content</div>
<script type="text/javascript">
$(document).ready(function(){
	$('#fadeOut').fadeIn();
});
</script>
HTML;

		$filter = new PSX_Html_Filter($html, new PSX_Html_Filter_Collection_Html5Inline());

		$this->assertEquals($expected, $filter->filter());
	}

	/**
	 * The a tag has an transparent content that means it uses the allowed types 
	 * from the parent element in this case span wich does not allow div the
	 * element. In the seconds case it has an div as parent wich allows div 
	 * elemnts as childs
	 */
	public function testTransparentContent()
	{
		$html = <<<HTML
<span>
	<a href="#">Foo <div>Bar</div></a>
</span>
HTML;

		$expected = <<<HTML
<span>
	<a href="#">Foo </a>
</span>
HTML;

		$filter = new PSX_Html_Filter($html, new PSX_Html_Filter_Collection_Html5Inline());

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<div>
	<a href="#">Foo <div>Bar</div></a>
</div>
HTML;

		$expected = <<<HTML
<div>
	<a href="#">Foo <div>Bar</div></a>
</div>
HTML;

		$filter = new PSX_Html_Filter($html, new PSX_Html_Filter_Collection_Html5Inline());

		$this->assertEquals($expected, $filter->filter());
	}

	/**
	 * Testing if the parent of an element has also a transparent content that 
	 * the filter searches the dom up for the allowed content
	 */
	public function testTransparentContentNested()
	{
		$html = <<<HTML
<span>
	<a href="#"><div>Foo</div>Foo <del>Bar <div>Foo</div></del></a>
</span>
HTML;

		$expected = <<<HTML
<span>
	<a href="#">Foo <del>Bar </del></a>
</span>
HTML;

		$filter = new PSX_Html_Filter($html, new PSX_Html_Filter_Collection_Html5Inline());

		$this->assertEquals($expected, $filter->filter());


		$html = <<<HTML
<div>
	<a href="#">Foo <del>Bar <div>Foo</div></del></a>
</div>
HTML;

		$expected = <<<HTML
<div>
	<a href="#">Foo <del>Bar <div>Foo</div></del></a>
</div>
HTML;

		$filter = new PSX_Html_Filter($html, new PSX_Html_Filter_Collection_Html5Inline());

		$this->assertEquals($expected, $filter->filter());
	}
}
