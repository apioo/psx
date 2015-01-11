<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Data\Writer;

use PSX\Http\MediaType;
use PSX\Loader\ReverseRouter;
use PSX\TemplateInterface;

/**
 * SvgTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

		$this->assertTrue($writer->isContentTypeSupported(MediaType::parse('image/svg+xml')));
		$this->assertFalse($writer->isContentTypeSupported(MediaType::parse('text/html')));
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
}
