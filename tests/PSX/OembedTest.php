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

namespace PSX;

use PSX\Http\Authentication;
use PSX\Http\GetRequest;
use PSX\Http\Handler\Callback;
use PSX\Http\Response;
use PSX\Http\ResponseParser;
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
	public function testLinkRequest()
	{
		$testCase = $this;
		$http = new Http(new Callback(function($request) use ($testCase){

			$response = <<<TEXT
HTTP/1.1 200 OK
Date: Thu, 26 Sep 2013 16:36:26 GMT
Content-Type: application/json; charset=UTF-8

{
  "type":"link",
  "version":"1.0",
  "title":"Beethoven - Rondo 'Die wut ueber den verlorenen groschen'",
  "author_name":"LukasSchuch",
  "author_url":"http:\/\/www.youtube.com\/user\/LukasSchuch",
  "provider_name":"YouTube",
  "provider_url":"http:\/\/www.youtube.com\/",
  "thumbnail_url":"http:\/\/i2.ytimg.com\/vi\/AKjzEG1eItY\/hqdefault.jpg",
  "thumbnail_width":480,
  "thumbnail_height":360
}
TEXT;

			return Response::convert($response, ResponseParser::MODE_LOOSE)->toString();

		}));

		$oembed = new Oembed($http);
		$url    = new Url('http://127.0.0.1/oembed/link?url=http%3A%2F%2Flocalhost.com%2Fresource');
		$type   = $oembed->request($url);

		$this->assertInstanceof('PSX\Oembed\Type\Link', $type);
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
		$testCase = $this;
		$http = new Http(new Callback(function($request) use ($testCase){

			$response = <<<TEXT
HTTP/1.1 200 OK
Date: Thu, 26 Sep 2013 16:36:26 GMT
Content-Type: application/json; charset=UTF-8

{
  "type":"photo",
  "version":"1.0",
  "title":"Beethoven - Rondo 'Die wut ueber den verlorenen groschen'",
  "author_name":"LukasSchuch",
  "author_url":"http:\/\/www.youtube.com\/user\/LukasSchuch",
  "provider_name":"YouTube",
  "provider_url":"http:\/\/www.youtube.com\/",
  "thumbnail_url":"http:\/\/i2.ytimg.com\/vi\/AKjzEG1eItY\/hqdefault.jpg",
  "thumbnail_width":480,
  "thumbnail_height":360,
  "url":"http:\/\/i2.ytimg.com\/vi\/AKjzEG1eItY\/hqdefault.jpg",
  "width":240,
  "height":160
}
TEXT;

			return Response::convert($response, ResponseParser::MODE_LOOSE)->toString();

		}));

		$oembed = new Oembed($http);
		$url    = new Url('http://127.0.0.1/oembed/photo?url=http%3A%2F%2Flocalhost.com%2Fresource');
		$type   = $oembed->request($url);

		$this->assertInstanceof('PSX\Oembed\Type\Photo', $type);
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
		$testCase = $this;
		$http = new Http(new Callback(function($request) use ($testCase){

			$response = <<<TEXT
HTTP/1.1 200 OK
Date: Thu, 26 Sep 2013 16:36:26 GMT
Content-Type: application/json; charset=UTF-8

{
  "type":"rich",
  "version":"1.0",
  "title":"Beethoven - Rondo 'Die wut ueber den verlorenen groschen'",
  "author_name":"LukasSchuch",
  "author_url":"http:\/\/www.youtube.com\/user\/LukasSchuch",
  "provider_name":"YouTube",
  "provider_url":"http:\/\/www.youtube.com\/",
  "thumbnail_url":"http:\/\/i2.ytimg.com\/vi\/AKjzEG1eItY\/hqdefault.jpg",
  "thumbnail_width":480,
  "thumbnail_height":360,
  "html":"<iframe width=\"459\" height=\"344\" src=\"http:\/\/www.youtube.com\/embed\/AKjzEG1eItY?fs=1&feature=oembed\" frameborder=\"0\" allowfullscreen><\/iframe>",
  "width":240,
  "height":160
}
TEXT;

			return Response::convert($response, ResponseParser::MODE_LOOSE)->toString();

		}));

		$oembed = new Oembed($http);
		$url    = new Url('http://127.0.0.1/oembed/rich?url=http%3A%2F%2Flocalhost.com%2Fresource');
		$type   = $oembed->request($url);

		$this->assertInstanceof('PSX\Oembed\Type\Rich', $type);
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
		$testCase = $this;
		$http = new Http(new Callback(function($request) use ($testCase){

			$response = <<<TEXT
HTTP/1.1 200 OK
Date: Thu, 26 Sep 2013 16:36:26 GMT
Content-Type: application/json; charset=UTF-8

{
  "type":"video",
  "version":"1.0",
  "title":"Beethoven - Rondo 'Die wut ueber den verlorenen groschen'",
  "author_name":"LukasSchuch",
  "author_url":"http:\/\/www.youtube.com\/user\/LukasSchuch",
  "provider_name":"YouTube",
  "provider_url":"http:\/\/www.youtube.com\/",
  "thumbnail_url":"http:\/\/i2.ytimg.com\/vi\/AKjzEG1eItY\/hqdefault.jpg",
  "thumbnail_width":480,
  "thumbnail_height":360,
  "html":"<iframe width=\"459\" height=\"344\" src=\"http:\/\/www.youtube.com\/embed\/AKjzEG1eItY?fs=1&feature=oembed\" frameborder=\"0\" allowfullscreen><\/iframe>",
  "width":240,
  "height":160
}
TEXT;

			return Response::convert($response, ResponseParser::MODE_LOOSE)->toString();

		}));

		$oembed = new Oembed($http);
		$url    = new Url('http://127.0.0.1/oembed/video?url=http%3A%2F%2Flocalhost.com%2Fresource');
		$type   = $oembed->request($url);

		$this->assertInstanceof('PSX\Oembed\Type\Video', $type);
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

	/**
	 * @expectedException Exception
	 */
	public function testRequestWithoutUrlParameter()
	{
		$this->oembed->request(new Url(self::URL));
	}

	public function testFindTag()
	{
		$html = <<<'HTML'
<html>
<head>
	<link rel="dns-prefetch" href="//yui.yahooapis.com">
	<meta name="keywords" content="birds, texas, pasadena, wilsonssnipe, kayakphotography, gseloff, horsepenbayou, galvestonbayestuary">	<meta name="description" content="Wilson's Snipes poking into the mud of the lagoon on Horsepen Bayou">
	<meta http-equiv="imagetoolbar" content="no">
	<link href="/apple-touch-icon.png" rel="apple-touch-icon-precomposed">
	<meta http-equiv="X-UA-Compatible" content="chrome=1">	<meta name="title" content="Double Down">
	<meta name="medium" content="image">
	<link rel="image_src" href="http://farm6.staticflickr.com/5483/11737652275_1c5882831c_m.jpg" id="image-src">
	<link id="canonicalurl" rel="canonical" href="http://www.flickr.com/photos/gseloff/11737652275/">
	<link rel="alternate" type="application/json+oembed"
				  href="http://www.flickr.com/services/oembed?url=http%3A%2F%2Fwww.flickr.com%2Fphotos%2Fgseloff%2F11737652275%2F&format=json"
				  title="Double Down"/>
	<link rel="alternate" type="text/xml+oembed"
				  href="http://www.flickr.com/services/oembed?url=http%3A%2F%2Fwww.flickr.com%2Fphotos%2Fgseloff%2F11737652275%2F&format=xml"
				  title="Double Down"/>
</head>
</html>
HTML;

		$url = Oembed::findTag($html);

		$this->assertEquals('http://www.flickr.com/services/oembed?url=http%3A%2F%2Fwww.flickr.com%2Fphotos%2Fgseloff%2F11737652275%2F&format=json', $url);
	}
}

