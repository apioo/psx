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

namespace PSX\Data;

/**
 * WriterFactoryTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class WriterFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $writerFactory;

    public function setUp()
    {
        $template = $this->getMockBuilder('PSX\TemplateInterface')
            ->getMock();

        $reverseRouter = $this->getMockBuilder('PSX\Loader\ReverseRouter')
            ->disableOriginalConstructor()
            ->getMock();

        $this->writerFactory = new WriterFactory();
        $this->writerFactory->addWriter(new Writer\Json(), 48);
        $this->writerFactory->addWriter(new Writer\Html($template, $reverseRouter), 40);
        $this->writerFactory->addWriter(new Writer\Atom(), 32);
        $this->writerFactory->addWriter(new Writer\Form(), 24);
        $this->writerFactory->addWriter(new Writer\Jsonp(), 16);
        $this->writerFactory->addWriter(new Writer\Soap('http://phpsx.org/2014/data'), 8);
        $this->writerFactory->addWriter(new Writer\Xml(), 0);
    }

    public function testGetDefaultWriter()
    {
        $this->assertInstanceOf('PSX\Data\Writer\Json', $this->writerFactory->getDefaultWriter());
    }

    public function testGetDefaultWriterEmpty()
    {
        $writerFactory = new WriterFactory();

        $this->assertNull($writerFactory->getDefaultWriter());
    }

    public function testGetWriterByContentType()
    {
        $this->assertInstanceOf('PSX\Data\Writer\Json', $this->writerFactory->getWriterByContentType('application/json'));
        $this->assertInstanceOf('PSX\Data\Writer\Html', $this->writerFactory->getWriterByContentType('text/html'));
        $this->assertInstanceOf('PSX\Data\Writer\Atom', $this->writerFactory->getWriterByContentType('application/atom+xml'));
        $this->assertInstanceOf('PSX\Data\Writer\Form', $this->writerFactory->getWriterByContentType('application/x-www-form-urlencoded'));
        $this->assertInstanceOf('PSX\Data\Writer\Jsonp', $this->writerFactory->getWriterByContentType('application/javascript'));
        $this->assertInstanceOf('PSX\Data\Writer\Soap', $this->writerFactory->getWriterByContentType('application/soap+xml'));
        $this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByContentType('application/xml'));
        $this->assertNull($this->writerFactory->getWriterByContentType('application/foo'));
        $this->assertNull($this->writerFactory->getWriterByContentType('application/json', array('PSX\Data\Writer\Xml')));
    }

    public function testGetWriterByContentTypeSupportedWriter()
    {
        $contentType = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';

        $this->assertInstanceOf('PSX\Data\Writer\Html', $this->writerFactory->getWriterByContentType($contentType));
        $this->assertInstanceOf('PSX\Data\Writer\Html', $this->writerFactory->getWriterByContentType($contentType, array('PSX\Data\Writer\Html')));
        $this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByContentType($contentType, array('PSX\Data\Writer\Xml')));
        $this->assertNull($this->writerFactory->getWriterByContentType($contentType, array('PSX\Data\Writer\Json')));
    }

    public function testGetWriterByContentTypeOrder()
    {
        $supportedWriter = array('PSX\Data\Writer\Xml');
        $contentType     = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';

        $this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByContentType($contentType, $supportedWriter));
    }

    public function testGetWriterByFormat()
    {
        $this->assertInstanceOf('PSX\Data\Writer\Json', $this->writerFactory->getWriterByFormat('json'));
        $this->assertInstanceOf('PSX\Data\Writer\Html', $this->writerFactory->getWriterByFormat('html'));
        $this->assertInstanceOf('PSX\Data\Writer\Atom', $this->writerFactory->getWriterByFormat('atom'));
        $this->assertInstanceOf('PSX\Data\Writer\Form', $this->writerFactory->getWriterByFormat('form'));
        $this->assertInstanceOf('PSX\Data\Writer\Jsonp', $this->writerFactory->getWriterByFormat('jsonp'));
        $this->assertInstanceOf('PSX\Data\Writer\Soap', $this->writerFactory->getWriterByFormat('soap'));
        $this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByFormat('xml'));
        $this->assertNull($this->writerFactory->getWriterByFormat('foo'));
        $this->assertNull($this->writerFactory->getWriterByFormat('json', array('PSX\Data\Writer\Xml')));
    }

    public function testGetWriterByInstance()
    {
        $this->assertInstanceOf('PSX\Data\Writer\Json', $this->writerFactory->getWriterByInstance('PSX\Data\Writer\Json'));
        $this->assertInstanceOf('PSX\Data\Writer\Html', $this->writerFactory->getWriterByInstance('PSX\Data\Writer\Html'));
        $this->assertInstanceOf('PSX\Data\Writer\Atom', $this->writerFactory->getWriterByInstance('PSX\Data\Writer\Atom'));
        $this->assertInstanceOf('PSX\Data\Writer\Form', $this->writerFactory->getWriterByInstance('PSX\Data\Writer\Form'));
        $this->assertInstanceOf('PSX\Data\Writer\Jsonp', $this->writerFactory->getWriterByInstance('PSX\Data\Writer\Jsonp'));
        $this->assertInstanceOf('PSX\Data\Writer\Soap', $this->writerFactory->getWriterByInstance('PSX\Data\Writer\Soap'));
        $this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByInstance('PSX\Data\Writer\Xml'));
        $this->assertNull($this->writerFactory->getWriterByInstance('PSX\Data\Writer\Foo'));
    }

    public function testContentNegotiationExplicit()
    {
        $this->writerFactory->setContentNegotiation('text/plain', WriterInterface::XML);

        $this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByContentType('text/plain'));
    }

    public function testContentNegotiationWildcardSubtype()
    {
        $this->writerFactory->setContentNegotiation('text/*', WriterInterface::XML);

        $this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByContentType('text/plain'));
        $this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByContentType('text/foo'));
        $this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByContentType('application/xml'));
        $this->assertInstanceOf('PSX\Data\Writer\Json', $this->writerFactory->getWriterByContentType('application/json'));
        $this->assertNull($this->writerFactory->getWriterByContentType('image/png'));
    }

    public function testContentNegotiationAll()
    {
        $this->writerFactory->setContentNegotiation('*/*', WriterInterface::XML);

        $this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByContentType('text/plain'));
        $this->assertInstanceOf('PSX\Data\Writer\Xml', $this->writerFactory->getWriterByContentType('application/json'));
    }

    public function testContentNegotiation()
    {
        $this->writerFactory->setContentNegotiation('image/*', WriterInterface::HTML);

        $this->assertInstanceOf('PSX\Data\Writer\Html', $this->writerFactory->getWriterByContentType('image/webp,*/*;q=0.8'));
        $this->assertInstanceOf('PSX\Data\Writer\Html', $this->writerFactory->getWriterByContentType('image/png, image/svg+xml, image/*;q=0.8, */*;q=0.5'));
        $this->assertInstanceOf('PSX\Data\Writer\Html', $this->writerFactory->getWriterByContentType('text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'));
    }

    /**
     * Check whether we serve known browsers with html
     *
     * @dataProvider browserAcceptHeaderProvider
     */
    public function testBrowserAcceptHeaders($contentType, $className)
    {
        $this->assertInstanceOf($className, $this->writerFactory->getWriterByContentType($contentType));
    }

    public function browserAcceptHeaderProvider()
    {
        return [
            ['text/html, application/xhtml+xml, */*', 'PSX\Data\Writer\Html'], // IE Version 11.0
            ['text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8', 'PSX\Data\Writer\Html'], // Chrome Version 43.0
        ];
    }
}
