<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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
 * ConfigTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
	protected $config;

	protected function setUp()
	{
		$this->config = getContainer()->get('config');
	}

	protected function tearDown()
	{
		unset($this->config);
	}

	public function testConfigOffsetSet()
	{
		$this->config['foo'] = 'bar';

		$this->assertEquals('bar', $this->config['foo']);

		$this->config->set('bar', 'foo');

		$this->assertEquals('foo', $this->config['bar']);
	}

	public function testConfigOffsetExists()
	{
		$this->assertEquals(false, isset($this->config['foobar']));
		$this->assertEquals(false, $this->config->has('foobar'));

		$this->config['foobar'] = 'test';

		$this->assertEquals(true, isset($this->config['foobar']));
		$this->assertEquals(true, $this->config->has('foobar'));
	}

	public function testConfigOffsetUnset()
	{
		$this->config['bar'] = 'test';

		unset($this->config['bar']);

		$this->assertEquals(true, !isset($this->config['bar']));
	}

	public function testConfigOffsetGet()
	{
		$this->config['bar'] = 'test';

		$this->assertEquals('test', $this->config['bar']);
		$this->assertEquals('test', $this->config->get('bar'));
	}
}




