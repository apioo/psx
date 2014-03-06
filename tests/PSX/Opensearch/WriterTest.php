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

namespace PSX\Opensearch;

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
		$writer = new Writer('Web Search', 'Use Example.com to search the Web.');
		$writer->setTags(array('example', 'web'));
		$writer->setContact('admin@example.com');
		$writer->addUrl('http://example.com/?q={searchTerms}&pw={startPage?}&format=rss', 'application/rss+xml');

		$actual   = $writer->toString();
		$expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
   <ShortName>Web Search</ShortName>
   <Description>Use Example.com to search the Web.</Description>
   <Tags>example web</Tags>
   <Contact>admin@example.com</Contact>
   <Url type="application/rss+xml" 
        template="http://example.com/?q={searchTerms}&amp;pw={startPage?}&amp;format=rss"/>
</OpenSearchDescription>
XML;

		$this->assertXmlStringEqualsXmlString($expected, $actual);
	}

	public function testComplexWriter()
	{
		$writer = new Writer('Web Search', 'Use Example.com to search the Web.');
		$writer->setTags(array('example', 'web'));
		$writer->setContact('admin@example.com');
		$writer->addUrl('http://example.com/?q={searchTerms}&pw={startPage?}&format=atom', 'application/atom+xml');
		$writer->addUrl('http://example.com/?q={searchTerms}&pw={startPage?}&format=rss', 'application/rss+xml');
		$writer->addUrl('http://example.com/?q={searchTerms}&pw={startPage?}', 'text/html');
		$writer->setLongName('Example.com Web Search');
		$writer->addImage('http://example.com/websearch.png', 64, 64, 'image/png');
		$writer->addImage('http://example.com/websearch.ico', 16, 16, 'image/vnd.microsoft.icon');
		$writer->addQuery('example', 'cat');
		$writer->setDeveloper('Example.com Development Team');
		$writer->setAttribution('Search data Copyright 2005, Example.com, Inc., All Rights Reserved');
		$writer->setSyndicationRight('open');
		$writer->setAdultContent(false);
		$writer->setLanguage('en-us');
		$writer->setOutputEncoding('UTF-8');
		$writer->setInputEncoding('UTF-8');

		$actual   = $writer->toString();
		$expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
   <ShortName>Web Search</ShortName>
   <Description>Use Example.com to search the Web.</Description>
   <Tags>example web</Tags>
   <Contact>admin@example.com</Contact>
   <Url type="application/atom+xml"
        template="http://example.com/?q={searchTerms}&amp;pw={startPage?}&amp;format=atom"/>
   <Url type="application/rss+xml"
        template="http://example.com/?q={searchTerms}&amp;pw={startPage?}&amp;format=rss"/>
   <Url type="text/html" 
        template="http://example.com/?q={searchTerms}&amp;pw={startPage?}"/>
   <LongName>Example.com Web Search</LongName>
   <Image height="64" width="64" type="image/png">http://example.com/websearch.png</Image>
   <Image height="16" width="16" type="image/vnd.microsoft.icon">http://example.com/websearch.ico</Image>
   <Query role="example" searchTerms="cat" />
   <Developer>Example.com Development Team</Developer>
   <Attribution>Search data Copyright 2005, Example.com, Inc., All Rights Reserved</Attribution>
   <SyndicationRight>open</SyndicationRight>
   <AdultContent>false</AdultContent>
   <Language>en-us</Language>
   <OutputEncoding>UTF-8</OutputEncoding>
   <InputEncoding>UTF-8</InputEncoding>
</OpenSearchDescription>
XML;

		$this->assertXmlStringEqualsXmlString($expected, $actual);
	}
}
