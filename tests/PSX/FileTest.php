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

namespace PSX;

/**
 * FileTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
	private $path;

	protected function setUp()
	{
		$this->path = PSX_PATH_CACHE . '/' . $this->getFileName();
	}

	protected function tearDown()
	{
		//unlink($this->path);
	}

	public function testFileWrite()
	{
		$file = new File($this->path);
		$file = $file->openFile('w');

		$this->assertEquals($this->getFileName(), $file->getFilename());
		$this->assertEquals(PSX_PATH_CACHE, $file->getPath());
		$this->assertEquals(true, $file->isFile());
		$this->assertEquals(true, File::exists($this->path));

		$bytes = $file->fwrite('foobar');

		$this->assertEquals(6, $bytes);

		unset($file);

		$this->assertEquals('foobar', File::getContents($this->path));
	}

	/**
	 * @depends testFileWrite
	 */
	public function testFileRead()
	{
		$file = File::open($this->path, 'r');

		$buffer = '';

		while(!$file->eof())
		{
			$buffer.= $file->fgets();
		}

		$this->assertEquals('foobar', $buffer);

		// we are in read mode we cant write
		$bytes = $file->fwrite('something');

		$this->assertEquals(0, $bytes);


		unset($file);
	}

	public function testPutGetContents()
	{
		$this->assertEquals(2, File::putContents($this->path, 'Oo'));
		$this->assertEquals('Oo', File::getContents($this->path));
	}

	private function getFileName()
	{
		return 'FileTest.txt';
	}
}
