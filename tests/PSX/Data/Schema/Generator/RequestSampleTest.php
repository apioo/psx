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

namespace PSX\Data\Schema\Generator;

/**
 * RequestSampleTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RequestSampleTest extends GeneratorTestCase
{
	public function testGenerateJson()
	{
		$generator = new RequestSample(RequestSample::FORMAT_JSON);
		$generator->setData($this->getSampleData());
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
    "author": {
        "title": "foo"
    },
    "price": 4,
    "content": "foo"
}
JSON;

		$this->assertJsonStringEqualsJsonString($expect, $result);
	}

	public function testGenerateXml()
	{
		$generator = new RequestSample(RequestSample::FORMAT_XML);
		$generator->setData($this->getSampleData());
		$result    = $generator->generate($this->getSchema());

		$expect = <<<'XML'
<news>
 <tags>bar</tags>
 <tags>foo</tags>
 <receiver>
  <title>foo</title>
  <email>foo@bar.com</email>
 </receiver>
 <author>
  <title>foo</title>
 </author>
 <price>4</price>
 <content>foo</content>
</news>
XML;

		$this->assertXmlStringEqualsXmlString($expect, $result);
	}

	protected function getSampleData()
	{
		return array(
			'tags' => array('bar', 'foo'),
			'receiver' => array(array(
				'title' => 'foo',
				'email' => 'foo@bar.com',
			)),
			'author' => array(
				'title' => 'foo',
			),
			'price' => 4,
			'content' => 'foo',
		);
	}
}
