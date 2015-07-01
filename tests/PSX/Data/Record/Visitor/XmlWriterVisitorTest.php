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

namespace PSX\Data\Record\Visitor;

use PSX\Data\Record;
use PSX\Data\Record\GraphTraverser;
use XMLWriter;

/**
 * XmlWriterVisitor
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class XmlWriterVisitorTest extends VisitorTestCase
{
    public function testTraverse()
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startDocument('1.0', 'UTF-8');

        $graph = new GraphTraverser();
        $graph->traverse($this->getRecord(), new XmlWriterVisitor($writer));

        $this->assertXmlStringEqualsXmlString($this->getExpected(), $writer->outputMemory());
    }

    protected function getExpected()
    {
        return <<<XML
<?xml version="1.0"?>
<record>
 <id>1</id>
 <title>foobar</title>
 <active>true</active>
 <disabled>false</disabled>
 <rating>12.45</rating>
 <date>2014-01-01T12:34:47+01:00</date>
 <href>http://foo.com</href>
 <person>
  <title>Foo</title>
 </person>
 <category>
  <general>
   <news>
    <technic>Foo</technic>
   </news>
  </general>
 </category>
 <tags>bar</tags>
 <tags>foo</tags>
 <tags>test</tags>
 <entry>
  <title>bar</title>
 </entry>
 <entry>
  <title>foo</title>
 </entry>
</record>
XML;
    }
}
