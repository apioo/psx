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

namespace PSX\Data\Tests\Transformer;

use PSX\Data\Transformer\XmlArray;
use PSX\Data\Transformer\XmlValidator;
use PSX\Http\MediaType;
use PSX\Rss;

/**
 * XmlValidatorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class XmlValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $body = <<<INPUT
<test xmlns="http://phpsx.org">
	<foo>bar</foo>
	<bar>blub</bar>
</test>
INPUT;

        $dom = new \DOMDocument();
        $dom->loadXML($body);

        $transformer = new XmlValidator(__DIR__ . '/schema.xsd');

        $expect = new \stdClass();
        $expect->foo = 'bar';
        $expect->bar = 'blub';

        $data = $transformer->transform($dom);

        $this->assertInstanceOf('stdClass', $data);
        $this->assertEquals($expect, $data);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Element '{http://phpsx.org}test': Missing child element(s). Expected is ( {http://phpsx.org}bar ).
     */
    public function testInvalidData()
    {
        $body = <<<INPUT
<test xmlns="http://phpsx.org">
	<foo>bar</foo>
</test>
INPUT;

        $dom = new \DOMDocument();
        $dom->loadXML($body);

        $transformer = new XmlValidator(__DIR__ . '/schema.xsd');
        $transformer->transform($dom);
    }
}
