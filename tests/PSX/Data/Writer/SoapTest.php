<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Data\Writer;

use PSX\Data\ExceptionRecord;
use PSX\Data\WriterTestCase;
use PSX\Http\MediaType;

/**
 * SoapTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SoapTest extends WriterTestCase
{
    public function testWrite()
    {
        $writer = new Soap('http://foo.bar');
        $writer->setRequestMethod('GET');

        $actual = $writer->write($this->getRecord());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
	<getResponse xmlns="http://foo.bar">
	  <id>1</id>
	  <author>foo</author>
	  <title>bar</title>
	  <content>foobar</content>
	  <date>2012-03-11T13:37:21Z</date>
	</getResponse>
  </soap:Body>
</soap:Envelope>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
        $this->assertEquals('get', $writer->getRequestMethod());
    }

    public function testWriteResultSet()
    {
        $writer = new Soap('http://foo.bar');
        $writer->setRequestMethod('GET');

        $actual = $writer->write($this->getResultSet());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
	<getResponse xmlns="http://foo.bar">
	  <totalResults>2</totalResults>
	  <startIndex>0</startIndex>
	  <itemsPerPage>8</itemsPerPage>
	  <entry>
	    <id>1</id>
	    <author>foo</author>
	    <title>bar</title>
	    <content>foobar</content>
	    <date>2012-03-11T13:37:21Z</date>
	  </entry>
	  <entry>
	    <id>2</id>
	    <author>foo</author>
	    <title>bar</title>
	    <content>foobar</content>
	    <date>2012-03-11T13:37:21Z</date>
	  </entry>
	</getResponse>
  </soap:Body>
</soap:Envelope>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
        $this->assertEquals('get', $writer->getRequestMethod());
    }

    public function testWriteComplex()
    {
        $writer = new Soap('http://foo.bar');
        $writer->setRequestMethod('GET');

        $actual = $writer->write($this->getComplexRecord());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
	<getResponse xmlns="http://foo.bar">
	  <verb>post</verb>
	  <actor>
	    <id>tag:example.org,2011:martin</id>
	    <objectType>person</objectType>
	    <displayName>Martin Smith</displayName>
	    <url>http://example.org/martin</url>
	  </actor>
	  <object>
	    <id>tag:example.org,2011:abc123/xyz</id>
	    <url>http://example.org/blog/2011/02/entry</url>
	  </object>
	  <target>
	    <id>tag:example.org,2011:abc123</id>
	    <objectType>blog</objectType>
	    <displayName>Martin's Blog</displayName>
	    <url>http://example.org/blog/</url>
	  </target>
	  <published>2011-02-10T15:04:55Z</published>
	</getResponse>
  </soap:Body>
</soap:Envelope>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function testWriteEmpty()
    {
        $writer = new Soap('http://foo.bar');
        $actual = $writer->write($this->getEmptyRecord());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <Response xmlns="http://foo.bar"/>
  </soap:Body>
</soap:Envelope>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function testIsContentTypeSupported()
    {
        $writer = new Soap('http://foo.bar');

        $this->assertTrue($writer->isContentTypeSupported(new MediaType('application/soap+xml')));
        $this->assertFalse($writer->isContentTypeSupported(new MediaType('text/html')));
    }

    public function testGetContentType()
    {
        $writer = new Soap('http://foo.bar');

        $this->assertEquals('text/xml', $writer->getContentType());
    }

    public function testWriteExceptionRecord()
    {
        $record = new ExceptionRecord();
        $record->setSuccess(false);
        $record->setTitle('An error occured');
        $record->setMessage('Foobar');
        $record->setTrace('Foo');
        $record->setContext('Bar');

        $writer = new Soap('http://foo.bar');
        $writer->setRequestMethod('GET');

        $actual = $writer->write($record);

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
	<soap:Fault>
	  <faultcode>soap:Server</faultcode>
	  <faultstring>Foobar</faultstring>
	  <detail>
	    <error xmlns="http://foo.bar">
	      <success>false</success>
	      <title>An error occured</title>
	      <message>Foobar</message>
	      <trace>Foo</trace>
	      <context>Bar</context>
	    </error>
	  </detail>
	</soap:Fault>
  </soap:Body>
</soap:Envelope>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }
}
