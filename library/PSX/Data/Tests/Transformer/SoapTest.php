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

use PSX\Data\Transformer\Soap;
use PSX\Http\MediaType;

/**
 * SoapTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SoapTest extends \PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $body = <<<INPUT
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<test xmlns="http://phpsx.org/2014/data">
			<foo>bar</foo>
			<bar>blub</bar>
			<bar>bla</bar>
			<test>
				<foo>bar</foo>
			</test>
		</test>
	</soap:Body>
</soap:Envelope>
INPUT;

        $dom = new \DOMDocument();
        $dom->loadXML($body);

        $transformer = new Soap('http://phpsx.org/2014/data');

        $expect = new \stdClass();
        $expect->foo = 'bar';
        $expect->bar = ['blub', 'bla'];
        $expect->test = new \stdClass();
        $expect->test->foo = 'bar';

        $data = $transformer->transform($dom);

        $this->assertInstanceOf('stdClass', $data);
        $this->assertEquals($expect, $data);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testNoEnvelope()
    {
        $body = <<<INPUT
<test xmlns="http://phpsx.org/2014/data">
	<foo>bar</foo>
	<bar>blub</bar>
	<bar>bla</bar>
	<test>
		<foo>bar</foo>
	</test>
</test>
INPUT;

        $dom = new \DOMDocument();
        $dom->loadXML($body);

        $transformer = new Soap('http://phpsx.org/2014/data');
        $transformer->transform($dom);
    }

    public function testEmptyBody()
    {
        $body = <<<INPUT
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
	</soap:Body>
</soap:Envelope>
INPUT;

        $dom = new \DOMDocument();
        $dom->loadXML($body);

        $transformer = new Soap('http://phpsx.org/2014/data');

        $expect = new \stdClass();

        $data = $transformer->transform($dom);

        $this->assertInstanceOf('stdClass', $data);
        $this->assertEquals($expect, $data);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBodyWrongNamespace()
    {
        $body = <<<INPUT
<soap:Envelope xmlns:soap="http://www.w3.org/2001/12/soap-envelope">
	<soap:Body>
	</soap:Body>
</soap:Envelope>
INPUT;

        $dom = new \DOMDocument();
        $dom->loadXML($body);

        $transformer = new Soap('http://phpsx.org/2014/data');
        $transformer->transform($dom);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidData()
    {
        $transformer = new Soap('http://phpsx.org/2014/data');
        $transformer->transform(array());
    }
}
