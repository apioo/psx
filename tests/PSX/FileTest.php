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

namespace PSX;

/**
 * FileTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
