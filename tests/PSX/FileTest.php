<?php
/*
 *  $Id: FileTest.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

/**
 * PSX_FileTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
class PSX_FileTest extends PHPUnit_Framework_TestCase
{
	private $path;

	protected function setUp()
	{
		if(isset($_SERVER['HOME']) && $_SERVER['HOME'][0] == '/')
		{
			$this->path = '/tmp/' . __CLASS__ . '.txt';
		}
		else
		{
			$this->path = PSX_PATH_CACHE . '/' . __CLASS__ . '.txt';
		}
	}

	protected function tearDown()
	{
		//unlink($this->path);
	}

	public function testFileWrite()
	{
		$file = new PSX_File($this->path);
		$file = $file->openFile('w');

		$this->assertEquals($this->path, (string) $file);
		$this->assertEquals(true, $file->isFile());
		$this->assertEquals(true, PSX_File::exists($this->path));

		$bytes = $file->fwrite('foobar');

		$this->assertEquals(6, $bytes);

		unset($file);

		$this->assertEquals('foobar', PSX_File::getContents($this->path));
	}

	/**
	 * @depends testFileWrite
	 */
	public function testFileRead()
	{
		$file = PSX_File::open($this->path, 'r');

		$buffer = '';

		while(!$file->eof())
		{
			// we read two bytes because we have 6 bytes
			// complete and we want also test the eof method
			$buffer.= $file->fgets();
		}

		$this->assertEquals('foobar', $buffer);

		// we are in read mode we cant write
		$bytes = $file->fwrite('something');

		$this->assertEquals(0, $bytes);


		unset($file);
	}
}
