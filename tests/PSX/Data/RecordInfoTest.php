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

namespace PSX\Data;

/**
 * RecordInfoTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
