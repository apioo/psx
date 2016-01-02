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

namespace PSX;

use PSX\Data\Record;
use PSX\Template\ErrorException;

/**
 * TemplateTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class TemplateTest extends \PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $template = new Template();

        $template->setDir('tests/PSX/Template/files');
        $template->set('foo.htm');

        $this->assertEquals('tests/PSX/Template/files', $template->getDir());
        $this->assertEquals('foo.htm', $template->get());
        $this->assertTrue($template->hasFile());
        $this->assertTrue($template->isFileAvailable());
        $this->assertFalse($template->isAbsoluteFile());
        $this->assertEquals('tests/PSX/Template/files/foo.htm', $template->getFile());

        $template->assign('foo', 'bar');

        $content = $template->transform();

        $this->assertEquals('Hello bar', $content);
    }

    public function testTransformException()
    {
        $template = new Template();
        $template->setDir('tests/PSX/Template/files');
        $template->set('error.htm');

        try {
            $template->transform();

            $this->fail('Must throw an excetion');
        } catch (ErrorException $e) {
            $this->assertInstanceOf('RuntimeException', $e->getOriginException());
            $this->assertEquals('tests/PSX/Template/files/error.htm', $e->getTemplateFile());
            $this->assertEquals('foobar', $e->getRenderedHtml());
        }
    }
}
