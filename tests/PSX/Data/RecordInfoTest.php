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

namespace PSX\Data;

/**
 * RecordInfoTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RecordInfoTest extends \PHPUnit_Framework_TestCase
{
	public function testRecordInfo()
	{
		$info = new RecordInfo('record', array('title' => 'foo'));

		$this->assertEquals('record', $info->getName());
		$this->assertEquals(array('title' => 'foo'), $info->getFields());
		$this->assertTrue($info->hasField('title'));
		$this->assertFalse($info->hasField('foo'));
		$this->assertTrue($info->hasFields(['title']));
		$this->assertFalse($info->hasFields(['foo']));
		$this->assertEquals(array(), $info->getMissingFields(['title']));
		$this->assertEquals(array('title'), $info->getMissingFields(['foo']));

		$info->setName('bar');
		$info->setFields(array('bar' => 'foo'));

		$this->assertEquals('bar', $info->getName());
		$this->assertEquals(array('bar' => 'foo'), $info->getFields());
	}

	public function testRecordInfoParent()
	{
		$parent = new RecordInfo('record', array('bar' => 'foo'));
		$info   = new RecordInfo('record', array('title' => 'foo'), $parent);

		$this->assertEquals('record', $info->getName());
		$this->assertEquals(array('title' => 'foo', 'bar' => 'foo'), $info->getFields());
	}
}
