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

namespace PSX\Api\Documentation\Generator;

use PSX\Api\View;

/**
 * SampleTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SampleTest extends \PHPUnit_Framework_TestCase
{
	public function testGenerate()
	{
		$loader = $this->getMockBuilder('PSX\Api\Documentation\Generator\Sample\LoaderInterface')
			->getMock();

		$view = $this->getMockBuilder('PSX\Api\View')
			->getMock();

		$methods = View::getMethods();
		$types   = View::getTypes();

		$c = 0;
		foreach($methods as $method => $methodName)
		{
			foreach($types as $type => $typeName)
			{
				$value = $method | $type;

				$loader->expects($this->at($c))
					->method('get')
					->with($this->equalTo($value), $this->equalTo('/'))
					->will($this->returnValue($methodName . ' ' . $typeName));

				$c++;
			}
		}

		$sample = new Sample($loader);
		$data   = $sample->generate('/', $view);

		$expect = array(
			'get' => array(
				'request'   => 'GET Request',
				'response'  => 'GET Response',
				'parameter' => 'GET Parameter',
			),
			'post' => array(
				'request'   => 'POST Request',
				'response'  => 'POST Response',
				'parameter' => 'POST Parameter',
			),
			'put' => array(
				'request'   => 'PUT Request',
				'response'  => 'PUT Response',
				'parameter' => 'PUT Parameter',
			),
			'delete' => array(
				'request'   => 'DELETE Request',
				'response'  => 'DELETE Response',
				'parameter' => 'DELETE Parameter',
			),
		);

		$this->assertEquals($expect, $data->toArray());
	}
}
