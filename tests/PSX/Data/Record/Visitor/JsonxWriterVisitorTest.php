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

namespace PSX\Data\Record\Visitor;

use PSX\Data\Record;
use PSX\Data\Record\GraphTraverser;
use XMLWriter;

/**
 * JsonxWriterVisitorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class JsonxWriterVisitorTest extends VisitorTestCase
{
    public function testTraverse()
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument('1.0', 'UTF-8');

        $graph = new GraphTraverser();
        $graph->traverse($this->getRecord(), new JsonxWriterVisitor($writer));

        $this->assertXmlStringEqualsXmlString($this->getExpected(), $writer->outputMemory());
    }

    public function testTraverseNullValue()
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument('1.0', 'UTF-8');

        $data = new \stdClass();
        $data->foo = 'bar';
        $data->bar = null;

        $graph = new GraphTraverser();
        $graph->traverse($data, new JsonxWriterVisitor($writer));

        $expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
 <json:string name="foo">bar</json:string>
 <json:null name="bar" />
</json:object>
XML;

        $this->assertXmlStringEqualsXmlString($expect, $writer->outputMemory());
    }

    protected function getExpected()
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<json:object xmlns:json="http://www.ibm.com/xmlns/prod/2009/jsonx">
 <json:number name="id">1</json:number>
 <json:string name="title">foobar</json:string>
 <json:boolean name="active">true</json:boolean>
 <json:boolean name="disabled">false</json:boolean>
 <json:number name="rating">12.45</json:number>
 <json:string name="date">2014-01-01T12:34:47+01:00</json:string>
 <json:string name="href">http://foo.com</json:string>
 <json:object name="person">
  <json:string name="title">Foo</json:string>
 </json:object>
 <json:object name="category">
  <json:object name="general">
   <json:object name="news">
    <json:string name="technic">Foo</json:string>
   </json:object>
  </json:object>
 </json:object>
 <json:array name="tags">
  <json:string>bar</json:string>
  <json:string>foo</json:string>
  <json:string>test</json:string>
 </json:array>
 <json:array name="entry">
  <json:object>
   <json:string name="title">bar</json:string>
  </json:object>
  <json:object>
   <json:string name="title">foo</json:string>
  </json:object>
 </json:array>
</json:object>
XML;
    }
}
