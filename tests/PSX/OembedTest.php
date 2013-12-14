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

namespace PSX;

use PSX\Http\Handler\Mock;
use PSX\Http\Handler\MockCapture;
use PSX\Oembed\Type;

/**
 * OembedTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class OembedTest extends \PHPUnit_Framework_TestCase
{
	const URL        = 'http://test.phpsx.org/oembed/server';
	const TYPE_LINK  = 'http://test.phpsx.org/oembed/link';
	const TYPE_PHOTO = 'http://test.phpsx.org/oembed/photo';
	const TYPE_RICH  = 'http://test.phpsx.org/oembed/rich';
	const TYPE_VIDEO = 'http://test.phpsx.org/oembed/video';

	private $http;
	private $oembed;

	protected function setUp()
	{
		//$mockCapture = new MockCapture('tests/PSX/Oembed/oembed_http_fixture.xml');
		$mock = Mock::getByXmlDefinition('tests/PSX/Oembed/oembed_http_fixture.xml');

		$this->http   = new Http($mock);
		$this->oembed = new Oembed($this->http);
	}

	protected function tearDown()
	{
		unset($this->oembed);
		unset($this->http);
	}

	public function testLinkRequest()
	{
		$url  = new Url(self::URL);
		$url->addParam('url', self::TYPE_LINK);
		$type = $this->oembed->request($url);

		$this->assertEquals(true, $type instanceof Type\Link);
		$this->assertEquals('link', $type->getType());
		$this->assertEquals('1.0', $type->getVersion());
		$this->assertEquals('Beethoven - Rondo \'Die wut ueber den verlorenen groschen\'', $type->getTitle());
		$this->assertEquals('LukasSchuch', $type->getAuthorName());
		$this->assertEquals('http://www.youtube.com/user/LukasSchuch', $type->getAuthorUrl());
		$this->assertEquals('YouTube', $type->getProviderName());
		$this->assertEquals('http://www.youtube.com/', $type->getProviderUrl());
		$this->assertEquals('http://i2.ytimg.com/vi/AKjzEG1eItY/hqdefault.jpg', $type->getThumbnailUrl());
		$this->assertEquals('480', $type->getThumbnailWidth());
		$this->assertEquals('360', $type->getThumbnailHeight());
	}

	public function testPhotoRequest()
	{
		$url  = new Url(self::URL);
		$url->addParam('url', self::TYPE_PHOTO);
		$type = $this->oembed->request($url);

		$this->assertEquals(true, $type instanceof Type\Photo);
		$this->assertEquals('photo', $type->getType());
		$this->assertEquals('1.0', $type->getVersion());
		$this->assertEquals('Beethoven - Rondo \'Die wut ueber den verlorenen groschen\'', $type->getTitle());
		$this->assertEquals('LukasSchuch', $type->getAuthorName());
		$this->assertEquals('http://www.youtube.com/user/LukasSchuch', $type->getAuthorUrl());
		$this->assertEquals('YouTube', $type->getProviderName());
		$this->assertEquals('http://www.youtube.com/', $type->getProviderUrl());
		$this->assertEquals('http://i2.ytimg.com/vi/AKjzEG1eItY/hqdefault.jpg', $type->getThumbnailUrl());
		$this->assertEquals('480', $type->getThumbnailWidth());
		$this->assertEquals('360', $type->getThumbnailHeight());
		$this->assertEquals('http://i2.ytimg.com/vi/AKjzEG1eItY/hqdefault.jpg', $type->getUrl());
		$this->assertEquals('240', $type->getWidth());
		$this->assertEquals('160', $type->getHeight());
	}

	public function testRichRequest()
	{
		$url  = new Url(self::URL);
		$url->addParam('url', self::TYPE_RICH);
		$type = $this->oembed->request($url);

		$this->assertEquals(true, $type instanceof Type\Rich);
		$this->assertEquals('rich', $type->getType());
		$this->assertEquals('1.0', $type->getVersion());
		$this->assertEquals('Beethoven - Rondo \'Die wut ueber den verlorenen groschen\'', $type->getTitle());
		$this->assertEquals('LukasSchuch', $type->getAuthorName());
		$this->assertEquals('http://www.youtube.com/user/LukasSchuch', $type->getAuthorUrl());
		$this->assertEquals('YouTube', $type->getProviderName());
		$this->assertEquals('http://www.youtube.com/', $type->getProviderUrl());
		$this->assertEquals('http://i2.ytimg.com/vi/AKjzEG1eItY/hqdefault.jpg', $type->getThumbnailUrl());
		$this->assertEquals('480', $type->getThumbnailWidth());
		$this->assertEquals('360', $type->getThumbnailHeight());
		$this->assertEquals('<iframe width="459" height="344" src="http://www.youtube.com/embed/AKjzEG1eItY?fs=1&feature=oembed" frameborder="0" allowfullscreen></iframe>', $type->getHtml());
		$this->assertEquals('240', $type->getWidth());
		$this->assertEquals('160', $type->getHeight());
	}

	public function testVideoRequest()
	{
		$url  = new Url(self::URL);
		$url->addParam('url', self::TYPE_VIDEO);
		$type = $this->oembed->request($url);

		$this->assertEquals(true, $type instanceof Type\Video);
		$this->assertEquals('video', $type->getType());
		$this->assertEquals('1.0', $type->getVersion());
		$this->assertEquals('Beethoven - Rondo \'Die wut ueber den verlorenen groschen\'', $type->getTitle());
		$this->assertEquals('LukasSchuch', $type->getAuthorName());
		$this->assertEquals('http://www.youtube.com/user/LukasSchuch', $type->getAuthorUrl());
		$this->assertEquals('YouTube', $type->getProviderName());
		$this->assertEquals('http://www.youtube.com/', $type->getProviderUrl());
		$this->assertEquals('http://i2.ytimg.com/vi/AKjzEG1eItY/hqdefault.jpg', $type->getThumbnailUrl());
		$this->assertEquals('480', $type->getThumbnailWidth());
		$this->assertEquals('360', $type->getThumbnailHeight());
		$this->assertEquals('<iframe width="459" height="344" src="http://www.youtube.com/embed/AKjzEG1eItY?fs=1&feature=oembed" frameborder="0" allowfullscreen></iframe>', $type->getHtml());
		$this->assertEquals('240', $type->getWidth());
		$this->assertEquals('160', $type->getHeight());
	}
}

