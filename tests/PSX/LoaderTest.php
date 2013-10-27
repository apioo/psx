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

use ReflectionClass;
use PSX\Http\Request;
use PSX\Url;
use PSX\Loader\Location;
use PSX\Loader\LocationFinder\CallbackMethod;

/**
 * LoaderTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class LoaderTest extends \PHPUnit_Framework_TestCase
{
	public function testLoadIndexCall()
	{
		$testCase = $this;

		$loader = new Loader(getContainer());
		$loader->setLocationFinder(new CallbackMethod(function($path) use ($testCase){

			$testCase->assertEquals('foobar', $path);

			return new Location(md5($path), '/', new ReflectionClass('PSX\Loader\ProbeModule'));

		}));

		$path    = '/foobar';
		$request = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$module  = $loader->load($path, $request);

		$expect = array(
			'PSX\Loader\ProbeModule::__construct',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::getRequestFilter',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::onLoad',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::onGet',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::doIndex',
		);

		$this->assertEquals($expect, $module->getMethodsCalled());
	}

	public function testLoadDetailCall()
	{
		$testCase = $this;

		$loader = new Loader(getContainer());
		$loader->setLocationFinder(new CallbackMethod(function($path) use ($testCase){

			$testCase->assertEquals('foobar/detail/12', $path);

			return new Location(md5($path), '/detail/12', new ReflectionClass('PSX\Loader\ProbeModule'));

		}));

		$path    = '/foobar/detail/12';
		$request = new Request(new Url('http://127.0.0.1' . $path), 'GET');
		$module  = $loader->load($path, $request);

		$expect = array(
			'PSX\Loader\ProbeModule::__construct',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::getRequestFilter',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::onLoad',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::onGet',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::doShowDetails',
		);

		$this->assertEquals($expect, $module->getMethodsCalled());
		$this->assertEquals(array('id' => 12), $module->getFragments());
	}

	public function testLoadInsertCall()
	{
		$testCase = $this;

		$loader = new Loader(getContainer());
		$loader->setLocationFinder(new CallbackMethod(function($path) use ($testCase){

			$testCase->assertEquals('foobar', $path);

			return new Location(md5($path), '/', new ReflectionClass('PSX\Loader\ProbeModule'));

		}));

		$path    = '/foobar';
		$request = new Request(new Url('http://127.0.0.1' . $path), 'POST');
		$module  = $loader->load($path, $request);

		$expect = array(
			'PSX\Loader\ProbeModule::__construct',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::getRequestFilter',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::onLoad',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::onGet',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::doInsert',
		);

		$this->assertEquals($expect, $module->getMethodsCalled());
	}

	public function testLoadInsertNestedCall()
	{
		$testCase = $this;

		$loader = new Loader(getContainer());
		$loader->setLocationFinder(new CallbackMethod(function($path) use ($testCase){

			$testCase->assertEquals('foobar/foo', $path);

			return new Location(md5($path), '/foo', new ReflectionClass('PSX\Loader\ProbeModule'));

		}));

		$path    = '/foobar/foo';
		$request = new Request(new Url('http://127.0.0.1' . $path), 'POST');
		$module  = $loader->load($path, $request);

		$expect = array(
			'PSX\Loader\ProbeModule::__construct',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::getRequestFilter',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::onLoad',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::onGet',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::doInsertNested',
		);

		$this->assertEquals($expect, $module->getMethodsCalled());
	}

	public function testLoadUpdateCall()
	{
		$testCase = $this;

		$loader = new Loader(getContainer());
		$loader->setLocationFinder(new CallbackMethod(function($path) use ($testCase){

			$testCase->assertEquals('foobar', $path);

			return new Location(md5($path), '/', new ReflectionClass('PSX\Loader\ProbeModule'));

		}));

		$path    = '/foobar';
		$request = new Request(new Url('http://127.0.0.1' . $path), 'PUT');
		$module  = $loader->load($path, $request);

		$expect = array(
			'PSX\Loader\ProbeModule::__construct',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::getRequestFilter',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::onLoad',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::onGet',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::doUpdate',
		);

		$this->assertEquals($expect, $module->getMethodsCalled());
	}

	public function testLoadUpdateNestedCall()
	{
		$testCase = $this;

		$loader = new Loader(getContainer());
		$loader->setLocationFinder(new CallbackMethod(function($path) use ($testCase){

			$testCase->assertEquals('foobar/foo', $path);

			return new Location(md5($path), '/foo', new ReflectionClass('PSX\Loader\ProbeModule'));

		}));

		$path    = '/foobar/foo';
		$request = new Request(new Url('http://127.0.0.1' . $path), 'PUT');
		$module  = $loader->load($path, $request);

		$expect = array(
			'PSX\Loader\ProbeModule::__construct',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::getRequestFilter',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::onLoad',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::onGet',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::doUpdateNested',
		);

		$this->assertEquals($expect, $module->getMethodsCalled());
	}

	public function testLoadDeleteCall()
	{
		$testCase = $this;

		$loader = new Loader(getContainer());
		$loader->setLocationFinder(new CallbackMethod(function($path) use ($testCase){

			$testCase->assertEquals('foobar', $path);

			return new Location(md5($path), '/', new ReflectionClass('PSX\Loader\ProbeModule'));

		}));

		$path    = '/foobar';
		$request = new Request(new Url('http://127.0.0.1' . $path), 'DELETE');
		$module  = $loader->load($path, $request);

		$expect = array(
			'PSX\Loader\ProbeModule::__construct',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::getRequestFilter',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::onLoad',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::onGet',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::doDelete',
		);

		$this->assertEquals($expect, $module->getMethodsCalled());
	}

	public function testLoadDeleteNestedCall()
	{
		$testCase = $this;

		$loader = new Loader(getContainer());
		$loader->setLocationFinder(new CallbackMethod(function($path) use ($testCase){

			$testCase->assertEquals('foobar/foo', $path);

			return new Location(md5($path), '/foo', new ReflectionClass('PSX\Loader\ProbeModule'));

		}));

		$path    = '/foobar/foo';
		$request = new Request(new Url('http://127.0.0.1' . $path), 'DELETE');
		$module  = $loader->load($path, $request);

		$expect = array(
			'PSX\Loader\ProbeModule::__construct',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::getRequestFilter',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::onLoad',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::onGet',
			'PSX\Loader\ProbeModule::getStage',
			'PSX\Loader\ProbeModule::doDeleteNested',
		);

		$this->assertEquals($expect, $module->getMethodsCalled());
	}
}
