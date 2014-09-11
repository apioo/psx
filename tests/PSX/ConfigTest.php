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
	public function testConstructorArray()
	{
		$config = new Config(array(
			'foo' => 'bar'
		));

		$this->assertEquals('bar', $config['foo']);
		$this->assertEquals('bar', $config->get('foo'));
	}

	public function testDefinitionConfig()
	{
		$config = new Config('tests/PSX/Config/definition_config.php');

		$this->assertEquals('bar', $config['foo']);
		$this->assertEquals('bar', $config->get('foo'));
	}

	/**
	 * @expectedException PSX\Config\NotFoundException
	 */
	public function testDefinitionConfigInvalidType()
	{
		$config = new Config('tests/PSX/Config/definition_invalid_config_type.php');
	}

	public function testReturnConfig()
	{
		$config = new Config('tests/PSX/Config/return_config.php');

		$this->assertEquals('bar', $config['foo']);
		$this->assertEquals('bar', $config->get('foo'));
	}

	/**
	 * @expectedException PSX\Config\NotFoundException
	 */
	public function testReturnConfigInvalidType()
	{
		$config = new Config('tests/PSX/Config/return_invalid_config_type.php');
	}

	/**
	 * @expectedException PSX\Config\NotFoundException
	 */
	public function testNoConfig()
	{
		$config = new Config('tests/PSX/Config/no_config.php');
	}

	/**
	 * @expectedException ErrorException
	 */
	public function testConfigCFileNotExisting()
	{
		$config = new Config('tests/PSX/Config/foo_config.php');
	}

	public function testConfigOffsetSet()
	{
		$config = new Config(array());

		$config['foo'] = 'bar';

		$this->assertEquals('bar', $config['foo']);

		$config->set('bar', 'foo');

		$this->assertEquals('foo', $config['bar']);
	}

	public function testConfigOffsetExists()
	{
		$config = new Config(array());

		$this->assertEquals(false, isset($config['foobar']));
		$this->assertEquals(false, $config->has('foobar'));

		$config['foobar'] = 'test';

		$this->assertEquals(true, isset($config['foobar']));
		$this->assertEquals(true, $config->has('foobar'));
	}

	public function testConfigOffsetUnset()
	{
		$config = new Config(array());

		$config['bar'] = 'test';

		unset($config['bar']);

		$this->assertEquals(true, !isset($config['bar']));
	}

	public function testConfigOffsetGet()
	{
		$config = new Config(array());

		$config['bar'] = 'test';

		$this->assertEquals('test', $config['bar']);
		$this->assertEquals('test', $config->get('bar'));
	}
}




