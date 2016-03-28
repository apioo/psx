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

namespace PSX\Framework\Framework\Tests\Upload;

use PSX\Framework\Upload\File;

/**
 * FileTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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

        $file = $this->getMockBuilder('PSX\Framework\Upload\File')
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
     * @expectedException \PSX\Framework\Upload\Exception
     */
    public function testFileIniSize()
    {
        $this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_INI_SIZE);
    }

    /**
     * @expectedException \PSX\Framework\Upload\Exception
     */
    public function testFileFormSize()
    {
        $this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_FORM_SIZE);
    }

    /**
     * @expectedException \PSX\Framework\Upload\Exception
     */
    public function testFilePartial()
    {
        $this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_PARTIAL);
    }

    /**
     * @expectedException \PSX\Framework\Upload\Exception
     */
    public function testFileNoFile()
    {
        $this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_NO_FILE);
    }

    /**
     * @expectedException \PSX\Framework\Upload\Exception
     */
    public function testFileNoTmpDir()
    {
        $this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_NO_TMP_DIR);
    }
    
    /**
     * @expectedException \PSX\Framework\Upload\Exception
     */
    public function testFileCantWrite()
    {
        $this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_CANT_WRITE);
    }

    /**
     * @expectedException \PSX\Framework\Upload\Exception
     */
    public function testFileExtension()
    {
        $this->createFile('foo', 'text/plain', 12, 'bar', UPLOAD_ERR_EXTENSION);
    }

    /**
     * @expectedException \PSX\Framework\Upload\Exception
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
