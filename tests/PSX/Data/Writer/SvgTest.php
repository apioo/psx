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

use PSX\Http\MediaType;
use PSX\Loader\ReverseRouter;
use PSX\TemplateInterface;

/**
 * SvgTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SvgTest extends TemplateAbstractTestCase
{
	protected function getWriter(TemplateInterface $template, ReverseRouter $router)
	{
		return new Svg($template, $router);
	}

	public function testIsContentTypeSupported()
	{
		$template = $this->getMock('PSX\TemplateInterface');
		$router   = $this->getMockBuilder('PSX\Loader\ReverseRouter')
			->disableOriginalConstructor()
			->getMock();

		$writer = new Svg($template, $router);

		$this->assertTrue($writer->isContentTypeSupported(new MediaType('image/svg+xml')));
		$this->assertFalse($writer->isContentTypeSupported(new MediaType('text/html')));
	}

	public function testGetContentType()
	{
		$template = $this->getMock('PSX\TemplateInterface');
		$router   = $this->getMockBuilder('PSX\Loader\ReverseRouter')
			->disableOriginalConstructor()
			->getMock();

		$writer = new Svg($template, $router);

		$this->assertEquals('image/svg+xml', $writer->getContentType());
	}

	/**
	 * @expectedException \PSX\Http\Exception\UnsupportedMediaTypeException
	 */
	public function testFallbackGenerator()
	{
		// we have no svg generator

		parent::testFallbackGenerator();
	}
}
