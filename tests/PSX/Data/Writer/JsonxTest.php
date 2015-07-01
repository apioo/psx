<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

use PSX\Data\WriterTestCase;
use PSX\Http\MediaType;

/**
 * JsonxTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class JsonxTest extends WriterTestCase
{
    public function testWrite()
    {
        $writer = new Jsonx();
        $actual = $writer->write($this->getRecord());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
  <json:number name="id">1</json:number>
  <json:string name="author">foo</json:string>
  <json:string name="title">bar</json:string>
  <json:string name="content">foobar</json:string>
  <json:string name="date">2012-03-11T13:37:21Z</json:string>
</json:object>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function testWriteResultSet()
    {
        $writer = new Jsonx();
        $actual = $writer->write($this->getResultSet());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
  <json:number name="totalResults">2</json:number>
  <json:number name="startIndex">0</json:number>
  <json:number name="itemsPerPage">8</json:number>
  <json:array name="entry">
    <json:object>
      <json:number name="id">1</json:number>
      <json:string name="author">foo</json:string>
      <json:string name="title">bar</json:string>
      <json:string name="content">foobar</json:string>
      <json:string name="date">2012-03-11T13:37:21Z</json:string>
    </json:object>
    <json:object>
      <json:number name="id">2</json:number>
      <json:string name="author">foo</json:string>
      <json:string name="title">bar</json:string>
      <json:string name="content">foobar</json:string>
      <json:string name="date">2012-03-11T13:37:21Z</json:string>
    </json:object>
  </json:array>
</json:object>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function testWriteComplex()
    {
        $writer = new Jsonx();
        $actual = $writer->write($this->getComplexRecord());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
  <json:string name="verb">post</json:string>
  <json:object name="actor">
    <json:string name="id">tag:example.org,2011:martin</json:string>
    <json:string name="objectType">person</json:string>
    <json:string name="displayName">Martin Smith</json:string>
    <json:string name="url">http://example.org/martin</json:string>
  </json:object>
  <json:object name="object">
    <json:string name="id">tag:example.org,2011:abc123/xyz</json:string>
    <json:string name="url">http://example.org/blog/2011/02/entry</json:string>
  </json:object>
  <json:object name="target">
    <json:string name="id">tag:example.org,2011:abc123</json:string>
    <json:string name="objectType">blog</json:string>
    <json:string name="displayName">Martin's Blog</json:string>
    <json:string name="url">http://example.org/blog/</json:string>
  </json:object>
  <json:string name="published">2011-02-10T15:04:55Z</json:string>
</json:object>
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function testWriteEmpty()
    {
        $writer = new Jsonx();
        $actual = $writer->write($this->getEmptyRecord());

        $expect = <<<TEXT
<?xml version="1.0" encoding="UTF-8"?>
<json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx" />
TEXT;

        $this->assertXmlStringEqualsXmlString($expect, $actual);
    }

    public function testIsContentTypeSupported()
    {
        $writer = new Jsonx();

        $this->assertTrue($writer->isContentTypeSupported(new MediaType('application/jsonx+xml')));
        $this->assertFalse($writer->isContentTypeSupported(new MediaType('application/xml')));
    }

    public function testGetContentType()
    {
        $writer = new Jsonx();

        $this->assertEquals('application/jsonx+xml', $writer->getContentType());
    }
}
