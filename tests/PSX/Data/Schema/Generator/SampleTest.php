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

namespace PSX\Data\Schema\Generator;

/**
 * SampleTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SampleTest extends GeneratorTestCase
{
    public function testGenerateJson()
    {
        $generator = new Sample(Sample::FORMAT_JSON, $this->getSampleData());
        $result    = $generator->generate($this->getSchema());

        $expect = <<<'JSON'
{
    "tags": [
        "bar",
        "foo"
    ],
    "receiver": [
        {
            "title": "foo",
            "email": "foo@bar.com"
        }
    ],
    "read": true,
    "author": {
        "title": "foo",
        "locations": [{
        	"lat": 13,
        	"long": -37
        }]
    },
    "price": 3.8,
    "content": "foo"
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $result);
    }

    public function testGenerateXml()
    {
        $generator = new Sample(Sample::FORMAT_XML, $this->getSampleData());
        $result    = $generator->generate($this->getSchema());

        $expect = <<<'XML'
<news>
 <tags>bar</tags>
 <tags>foo</tags>
 <receiver>
  <title>foo</title>
  <email>foo@bar.com</email>
 </receiver>
 <read>true</read>
 <author>
  <title>foo</title>
  <locations>
   <lat>13</lat>
   <long>-37</long>
  </locations>
 </author>
 <price>3.8</price>
 <content>foo</content>
</news>
XML;

        $this->assertXmlStringEqualsXmlString($expect, $result);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGenerateMissingValue()
    {
        $generator = new Sample(Sample::FORMAT_JSON, []);
        $generator->generate($this->getSchema());
    }

    protected function getSampleData()
    {
        return array(
            'tags' => array('bar', 'foo'),
            'receiver' => array(
                array(
                    'title' => 'foo',
                    'email' => 'foo@bar.com',
                )
            ),
            'author' => array(
                'title' => 'foo',
                'locations' => array(
                    array(
                        'lat' => 13,
                        'long' => -37
                    ),
                )
            ),
            'price' => 3.8,
            'content' => 'foo',
            'read' => true,
        );
    }
}
