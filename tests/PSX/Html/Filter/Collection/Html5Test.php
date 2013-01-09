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
class PSX_Html_Filter_Collection_Html5Test extends PHPUnit_Framework_TestCase
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

		$filter = new PSX_Html_Filter($html, new PSX_Html_Filter_Collection_Html5());

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

		$filter = new PSX_Html_Filter($html, new PSX_Html_Filter_Collection_Html5());

		$this->assertEquals($expected, $filter->filter());
	}

	public function testDataAttribute()
	{
		$html = <<<HTML
<div id="foo" data-test="bar">foo</div>
HTML;

		$expected = <<<HTML
<div id="foo" data-test="bar">foo</div>
HTML;

		$filter = new PSX_Html_Filter($html, new PSX_Html_Filter_Collection_Html5());

		$this->assertEquals($expected, $filter->filter());
	}

	public function testInvalidDataAttribute()
	{
		$html = <<<HTML
<div id="foo" data-t&est="bar">foo</div>
HTML;

		$expected = <<<HTML
<div id="foo">foo</div>
HTML;

		$filter = new PSX_Html_Filter($html, new PSX_Html_Filter_Collection_Html5());

		$this->assertEquals($expected, $filter->filter());
	}
}
