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

namespace PSX\Upload;

/**
 * FileTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
	public function testFile()
	{
		$data = array(
			'name'     => 'upload.txt',
			'type'     => 'text/plain',
			'size'     => 12,
			'tmp_name' => '/tmp/tmp123',
			'error'    => UPLOAD_ERR_OK,
		);

		$file = $this->getMockBuilder('PSX\Upload\File')
			->setMethods(array('isUploadedFile', 'moveUploadedFile'))
			->getMock();

		$file->expects($this->once())
			->method('isUploadedFile')
			->with('/tmp/tmp123')
			->will($this->returnValue(true));

		$file->expects($this->once())
			->method('moveUploadedFile')
			->with('/tmp/tmp123', '/foo/bar')
			->will($this->returnValue(true));

		$file->setFile($data);

		$this->assertEquals('upload.txt', $file->getName());
		$this->assertEquals('text/plain', $file->getType());
		$this->assertEquals(12, $file->getSize());
		$this->assertEquals('/tmp/tmp123', $file->getTmpName());
		$this->assertEquals(UPLOAD_ERR_OK, $file->getError());

		$file->move('/foo/bar');
	}

	/**
	 * @expectedException PSX\Upload\Exception
	 */
	public function testFileIniSize()
	{
		$this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_INI_SIZE);
	}

	/**
	 * @expectedException PSX\Upload\Exception
	 */
	public function testFileFormSize()
	{
		$this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_FORM_SIZE);
	}

	/**
	 * @expectedException PSX\Upload\Exception
	 */
	public function testFilePartial()
	{
		$this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_PARTIAL);
	}

	/**
	 * @expectedException PSX\Upload\Exception
	 */
	public function testFileNoFile()
	{
		$this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_NO_FILE);
	}

	/**
	 * @expectedException PSX\Upload\Exception
	 */
	public function testFileNoTmpDir()
	{
		$this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_NO_TMP_DIR);
	}
	
	/**
	 * @expectedException PSX\Upload\Exception
	 */
	public function testFileCantWrite()
	{
		$this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_CANT_WRITE);
	}

	/**
	 * @expectedException PSX\Upload\Exception
	 */
	public function testFileExtension()
	{
		$this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_EXTENSION);
	}

	/**
	 * @expectedException PSX\Upload\Exception
	 */
	public function testFileUnknown()
	{
		$this->createFile('foo', 'text/plain', 12, 'bar', -1);
	}

	protected function createFile($name, $type, $size, $tmpName, $error)
	{
		return new File(array(
			'name'     => $name,
			'type'     => $type,
			'size'     => $size,
			'tmp_name' => $tmpName,
			'error'    => $error,
		));
	}
}
