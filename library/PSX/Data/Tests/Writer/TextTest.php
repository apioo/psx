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

namespace PSX\Data\Tests\Writer;

use PSX\Data\Writer\Text;
use PSX\Framework\Loader\ReverseRouter;
use PSX\Framework\Template\TemplateInterface;
use PSX\Http\MediaType;

/**
 * TextTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TextTest extends TemplateAbstractTestCase
{
    protected function getWriter(TemplateInterface $template, ReverseRouter $router)
    {
        return new Text($template, $router);
    }

    public function testIsContentTypeSupported()
    {
        $template = $this->getMock('PSX\Framework\Template\TemplateInterface');
        $router   = $this->getMockBuilder('PSX\Framework\Loader\ReverseRouter')
            ->disableOriginalConstructor()
            ->getMock();

        $writer = new Text($template, $router);

        $this->assertTrue($writer->isContentTypeSupported(new MediaType('text/plain')));
        $this->assertFalse($writer->isContentTypeSupported(new MediaType('text/html')));
    }

    public function testGetContentType()
    {
        $template = $this->getMock('PSX\Framework\Template\TemplateInterface');
        $router   = $this->getMockBuilder('PSX\Framework\Loader\ReverseRouter')
            ->disableOriginalConstructor()
            ->getMock();

        $writer = new Text($template, $router);

        $this->assertEquals('text/plain', $writer->getContentType());
    }

    /**
     * @expectedException \PSX\Http\Exception\UnsupportedMediaTypeException
     */
    public function testFallbackGenerator()
    {
        // we have no text generator

        parent::testFallbackGenerator();
    }
}
