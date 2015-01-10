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

namespace PSX;

use PSX\Data\Record;
use PSX\Template\ErrorException;

/**
 * TemplateTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TemplateTest extends \PHPUnit_Framework_TestCase
{
	public function testTransform()
	{
		$template = new Template();

		$template->setDir('tests/PSX/Template/files');
		$template->set('foo.htm');

		$this->assertTrue($template->hasFile());
		$this->assertEquals('foo.htm', $template->get());

		$template->assign('foo', 'bar');

		$content = $template->transform();

		$this->assertEquals('Hello bar', $content);
	}

	public function testFallbackTemplate()
	{
		$template = new Template();
		$template->assign('key_1', 'bar');
		$template->assign('key_2', array(1, 2));
		$template->assign('key_3', array(new Record('foo', array('bar' => 'foo'))));
		$template->assign('key_4', new Record('foo', array('bar' => 'foo')));
		$template->assign('key_5', new \DateTime('2014-12-27'));
		$template->assign('key_6', new StringObject());
		$template->assign('key_7', false);
		$template->assign('key_8', true);

		$content = $template->transform();

		preg_match('/<body>(.*)<\/body>/ims', $content, $matches);

		$this->assertArrayHasKey(1, $matches);
		$this->assertXmlStringEqualsXmlString($this->getExpectedFallbackTemplate(), $matches[1]);
	}

	public function testTransformException()
	{
		$template = new Template();
		$template->setDir('tests/PSX/Template/files');
		$template->set('error.htm');

		try
		{
			$template->transform();

			$this->fail('Must throw an excetion');
		}
		catch(ErrorException $e)
		{
			$this->assertInstanceOf('RuntimeException', $e->getOriginException());
			$this->assertEquals('tests/PSX/Template/files/error.htm', $e->getTemplateFile());
			$this->assertEquals('foobar', $e->getRenderedHtml());
		}
	}

	protected function getExpectedFallbackTemplate()
	{
		return <<<HTML
<dl>
	<dt>key_1</dt>
	<dd class="scalar">bar</dd>
	<dt>key_2</dt>
	<dd class="array">
		<ul>
			<li>1</li>
			<li>2</li>
		</ul>
	</dd>
	<dt>key_3</dt>
	<dd class="array">
		<ul>
			<li>
				<dl>
					<dt>bar</dt>
					<dd class="scalar">foo</dd>
				</dl>
			</li>
		</ul>
	</dd>
	<dt>key_4</dt>
	<dd class="object">
		<dl>
			<dt>bar</dt>
			<dd class="scalar">foo</dd>
		</dl>
	</dd>
	<dt>key_5</dt>
	<dd class="scalar">2014-12-27T00:00:00+00:00</dd>
	<dt>key_6</dt>
	<dd class="scalar">foo</dd>
	<dt>key_7</dt>
	<dd class="scalar">0</dd>
	<dt>key_8</dt>
	<dd class="scalar">1</dd>
</dl>
HTML;
	}
}

class StringObject
{
	public function __toString()
	{
		return 'foo';
	}
}
