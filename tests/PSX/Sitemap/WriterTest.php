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

namespace PSX\Sitemap;

use DateTime;
use PSX\Url;

/**
 * WriterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class WriterTest extends \PHPUnit_Framework_TestCase
{
	public function testWriter()
	{
		$writer = new Writer();
		$writer->addUrl(new Url('http://www.example.com/'), new DateTime('2005-01-01'), 'monthly', 0.8);

		$actual   = $writer->toString();
		$expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
   <url>
      <loc>http://www.example.com/</loc>
      <lastmod>2005-01-01T00:00:00+00:00</lastmod>
      <changefreq>monthly</changefreq>
      <priority>0.8</priority>
   </url>
</urlset> 
XML;

		$this->assertXmlStringEqualsXmlString($expected, $actual);
	}

	public function testComplexWriter()
	{
		$writer = new Writer();
		$writer->addUrl(new Url('http://www.example.com/'), new DateTime('2005-01-01'), 'monthly', 0.8);
		$writer->addUrl(new Url('http://www.example.com/catalog?item=12&desc=vacation_hawaii'), null, 'weekly');
		$writer->addUrl(new Url('http://www.example.com/catalog?item=73&desc=vacation_new_zealand'), new DateTime('2004-12-23'), 'weekly');
		$writer->addUrl(new Url('http://www.example.com/catalog?item=74&desc=vacation_newfoundland'), new DateTime('2004-12-23T18:00:15+00:00'), null, 0.3);
		$writer->addUrl(new Url('http://www.example.com/catalog?item=83&desc=vacation_usa'), new DateTime('2004-11-23'));

		$actual   = $writer->toString();
		$expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
   <url>
      <loc>http://www.example.com/</loc>
      <lastmod>2005-01-01T00:00:00+00:00</lastmod>
      <changefreq>monthly</changefreq>
      <priority>0.8</priority>
   </url>
   <url>
      <loc>http://www.example.com/catalog?item=12&amp;desc=vacation_hawaii</loc>
      <changefreq>weekly</changefreq>
   </url>
   <url>
      <loc>http://www.example.com/catalog?item=73&amp;desc=vacation_new_zealand</loc>
      <lastmod>2004-12-23T00:00:00+00:00</lastmod>
      <changefreq>weekly</changefreq>
   </url>
   <url>
      <loc>http://www.example.com/catalog?item=74&amp;desc=vacation_newfoundland</loc>
      <lastmod>2004-12-23T18:00:15+00:00</lastmod>
      <priority>0.3</priority>
   </url>
   <url>
      <loc>http://www.example.com/catalog?item=83&amp;desc=vacation_usa</loc>
      <lastmod>2004-11-23T00:00:00+00:00</lastmod>
   </url>
</urlset>
XML;

		$this->assertXmlStringEqualsXmlString($expected, $actual);
	}
}
