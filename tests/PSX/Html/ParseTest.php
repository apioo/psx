<?php
/*
 *  $Id: ParseTest.php 543 2012-07-10 21:38:34Z k42b3.x@googlemail.com $
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
 * PSX_Html_ParseTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 543 $
 */
class PSX_Html_ParseTest extends PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testParseAtomLink()
	{
		$html = <<<HTML
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
 <head>
  <title>admin (admin) – test</title>
  <link rel="shortcut icon" href="http://127.0.0.1/tests/statusnet/favicon.ico"/>
  <link rel="stylesheet" type="text/css" href="http://127.0.0.1/tests/statusnet/theme/base/css/display.css?version=1.0.1" media="screen, projection, tv, print"/>
  <link rel="stylesheet" type="text/css" href="http://127.0.0.1/tests/statusnet/theme/neo/css/display.css?version=1.0.1" media="screen, projection, tv, print"/>
  <link rel="stylesheet" type="text/css" href="http://127.0.0.1/tests/statusnet/plugins/OStatus/theme/base/css/ostatus.css" media=""/>
  <link rel="stylesheet" type="text/css" href="http://127.0.0.1/tests/statusnet/js/css/smoothness/jquery-ui.css" media=""/>
  <!--[if IE]><link rel="stylesheet" type="text/css" href="http://127.0.0.1/tests/statusnet/theme/base/css/ie.css?version=1.0.1" /><![endif]-->
  <!--[if lte IE 6]><link rel="stylesheet" type="text/css" href="http://127.0.0.1/tests/statusnet/theme/base/css/ie6.css?version=1.0.1" /><![endif]-->
  <!--[if lte IE 7]><link rel="stylesheet" type="text/css" href="http://127.0.0.1/tests/statusnet/theme/base/css/ie7.css?version=1.0.1" /><![endif]-->
  <link rel="stylesheet" type="text/css" href="http://127.0.0.1/tests/statusnet/plugins/Bookmark/bookmark.css" media=""/>
  <link rel="stylesheet" type="text/css" href="http://127.0.0.1/tests/statusnet/plugins/Event/event.css" media=""/>
  <link rel="stylesheet" type="text/css" href="http://127.0.0.1/tests/statusnet/plugins/Poll/poll.css" media=""/>
  <link rel="stylesheet" type="text/css" href="http://127.0.0.1/tests/statusnet/plugins/QnA/css/qna.css" media=""/>
  <link rel="search" type="application/opensearchdescription+xml" href="http://127.0.0.1/tests/statusnet/index.php/opensearch/people" title="test People Search"/>
  <link rel="search" type="application/opensearchdescription+xml" href="http://127.0.0.1/tests/statusnet/index.php/opensearch/notice" title="test Notice Search"/>
  <link rel="alternate" href="http://127.0.0.1/tests/statusnet/index.php/api/statuses/user_timeline/1.as" type="application/stream+json" title="Notice feed for admin (Activity Streams JSON)"/>
  <link rel="alternate" href="http://127.0.0.1/tests/statusnet/index.php/admin/rss" type="application/rdf+xml" title="Feed der Nachrichten von admin (RSS 1.0)"/>
  <link rel="alternate" href="http://127.0.0.1/tests/statusnet/index.php/api/statuses/user_timeline/1.rss" type="application/rss+xml" title="Feed der Nachrichten von admin (RSS 2.0)"/>
  <link rel="alternate" href="http://127.0.0.1/tests/statusnet/index.php/api/statuses/user_timeline/1.atom" type="application/atom+xml" title="Feed der Nachrichten von admin (Atom)"/>
  <link rel="meta" href="http://127.0.0.1/tests/statusnet/index.php/admin/foaf" type="application/rdf+xml" title="FOAF von admin"/>
  <link rel="microsummary" href="http://127.0.0.1/tests/statusnet/index.php/admin/microsummary"/>
  <link rel="EditURI" type="application/rsd+xml" href="http://127.0.0.1/tests/statusnet/index.php/rsd.xml"/>
  <link rel="openid2.provider" href="http://127.0.0.1/tests/statusnet/index.php/main/openidserver"/>
  <link rel="openid2.local_id" href="http://127.0.0.1/tests/statusnet/index.php/admin"/>
  <link rel="openid.server" href="http://127.0.0.1/tests/statusnet/index.php/main/openidserver"/>
  <link rel="openid.delegate" href="http://127.0.0.1/tests/statusnet/index.php/admin"/>
 </head>
 <body id="showstream">
 </body>
</html>
HTML;

		$parse  = new PSX_Html_Parse($html);
		$actual = $parse->fetchAttrFromHead(new PSX_Html_Parse_Element('link', array(

			'rel'  => 'alternate',
			'type' => 'application/atom+xml',

		)), 'href');

		$expect = 'http://127.0.0.1/tests/statusnet/index.php/api/statuses/user_timeline/1.atom';

		$this->assertEquals($expect, $actual);
	}

	public function testParseAtomLinkCrappyHtml()
	{
		$html = <<<HTML
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
 <head>
  <title>admin (admin) – test</title>
  <LINK REL=alternate HREF=http://127.0.0.1/tests/statusnet/index.php/api/statuses/user_timeline/1.atom TYPE="application/atom+xml"></LINK>
 </head>
 <body>
 </body>
</html>
HTML;

		$parse  = new PSX_Html_Parse($html);
		$actual = $parse->fetchAttrFromHead(new PSX_Html_Parse_Element('link', array(

			'rel'  => 'alternate',
			'type' => 'application/atom+xml',

		)), 'href');

		$expect = 'http://127.0.0.1/tests/statusnet/index.php/api/statuses/user_timeline/1.atom';

		$this->assertEquals($expect, $actual);
	}

	public function testGetHead()
	{
		$html = <<<HTML
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
 <head>
  <meta />
 </head>
 <body>
  <h1>foobar</h1>
 </body>
</html>
HTML;

		$expect = <<<HTML
<head>
  <meta />
 </head>
HTML;

		$parse = new PSX_Html_Parse($html);
		$head  = $parse->getHead();

		$this->assertEquals($expect, $head);
	}

	public function testGetBody()
	{
		$html = <<<HTML
<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
 <head>
  <meta />
 </head>
 <body>
  <h1>foobar</h1>
 </body>
</html>
HTML;

		$expect = <<<HTML
<body>
  <h1>foobar</h1>
 </body>
HTML;

		$parse = new PSX_Html_Parse($html);
		$head  = $parse->getBody();

		$this->assertEquals($expect, $head);
	}
}