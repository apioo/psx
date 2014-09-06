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

namespace PSX\Data\Transformer;

/**
 * CallbackTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CallbackTest extends \PHPUnit_Framework_TestCase
{
	public function testTransform()
	{
		$transformer = new Callback(function($data){

			return array(
				'foo' => md5($data)
			);

		});

		$data = 'some data format';

		$result = $transformer->transform($data);

		$this->assertEquals(array('foo' => '791a818e1f5aef6ae38ada7b7317c69a'), $result);
	}

	public function testAccept()
	{
		$transformer = new Callback(function(){}, 'text/plain');

		$this->assertTrue($transformer->accept('text/plain'));
		$this->assertFalse($transformer->accept('foo'));
	}

	public function testAcceptCallback()
	{
		$transformer = new Callback(function(){}, function($contentType){

			return $contentType == 'text/plain';

		});

		$this->assertTrue($transformer->accept('text/plain'));
		$this->assertFalse($transformer->accept('foo'));
	}
}
