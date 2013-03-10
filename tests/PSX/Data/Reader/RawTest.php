<?php
/*
 *  $Id: RawTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Data\Reader;

use PSX\Data\ReaderInterface;
use PSX\Http\Message;

/**
 * PSX_Data_Reader_RawTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class RawTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testRead()
	{
		$body = <<<INPUT
some foobar content
INPUT;

		$reader  = new Raw();
		$message = new Message(array(), $body);

		$result = $reader->read($message);
		$raw    = $result->getData();

		$e = 'some foobar content';

		$this->assertEquals(ReaderInterface::RAW, $result->getType());
		$this->assertEquals(true, $raw instanceof Message);
		$this->assertEquals($e, $raw->getBody());
	}
}
