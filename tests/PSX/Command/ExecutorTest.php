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

namespace PSX\Command;

use PSX\Event;
use PSX\CommandAbstract;
use PSX\Command\OutputInterface;
use PSX\Command\Output\Void;
use PSX\Command\ParameterParser\Map;
use PSX\Loader\Location;
use PSX\Test\CommandTestCase;

/**
 * ExecutorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ExecutorTest extends CommandTestCase
{
	public function testRun()
	{
		$testCase = $this;
		$command = new TestCommand(function(Parameters $parameters) use ($testCase){

			$testCase->assertTrue($parameters->has('r'));
			$testCase->assertEquals('foo', $parameters->get('r'));
			$testCase->assertTrue($parameters->has('o'));
			$testCase->assertEquals('bar', $parameters->get('o'));
			$testCase->assertTrue($parameters->has('f'));
			$testCase->assertEquals(true, $parameters->get('f'));

		});

		// test events
		$commandExecuteListener = $this->getMock('PSX\Dispatch\TestListener', array('on'));
		$commandExecuteListener->expects($this->once())
			->method('on')
			->with($this->callback(function($event) use ($testCase){
				$testCase->assertInstanceOf('PSX\Event\CommandExecuteEvent', $event);
				$testCase->assertInstanceOf('PSX\CommandInterface', $event->getCommand());
				$testCase->assertInstanceOf('PSX\Command\Parameters', $event->getParameters());
				$testCase->assertEquals('foo', $event->getParameters()->get('r'));
				$testCase->assertEquals('bar', $event->getParameters()->get('o'));
				$testCase->assertEquals(true, $event->getParameters()->get('f'));

				return true;
			}));

		$commandProcessedListener = $this->getMock('PSX\Dispatch\TestListener', array('on'));
		$commandProcessedListener->expects($this->once())
			->method('on')
			->with($this->callback(function($event) use ($testCase){
				$testCase->assertInstanceOf('PSX\Event\CommandProcessedEvent', $event);
				$testCase->assertInstanceOf('PSX\CommandInterface', $event->getCommand());
				$testCase->assertInstanceOf('PSX\Command\Parameters', $event->getParameters());
				$testCase->assertEquals('foo', $event->getParameters()->get('r'));
				$testCase->assertEquals('bar', $event->getParameters()->get('o'));
				$testCase->assertEquals(true, $event->getParameters()->get('f'));

				return true;
			}));

		getContainer()->get('event_dispatcher')->addListener(Event::COMMAND_EXECUTE, array($commandExecuteListener, 'on'));
		getContainer()->get('event_dispatcher')->addListener(Event::COMMAND_PROCESSED, array($commandProcessedListener, 'on'));

		$factory = $this->getMockBuilder('PSX\Dispatch\CommandFactoryInterface')
			->setMethods(array('getCommand'))
			->getMock();

		$factory->expects($this->once())
			->method('getCommand')
			->with($this->identicalTo('Foo\Bar'))
			->will($this->returnValue($command));

		$executor = new Executor($factory, new Void(), getContainer()->get('event_dispatcher'));
		$executor->run(new Map('Foo\Bar', array('r' => 'foo', 'o' => 'bar', 'f' => true)));

		getContainer()->get('event_dispatcher')->removeListener(Event::COMMAND_EXECUTE, $commandExecuteListener);
		getContainer()->get('event_dispatcher')->removeListener(Event::COMMAND_PROCESSED, $commandProcessedListener);
	}

	/**
	 * @expectedException PSX\Command\MissingParameterException
	 */
	public function testRunMissingRequiredParameter()
	{
		$testCase = $this;
		$command = new TestCommand(function(Parameters $parameters) use ($testCase){
		});

		$factory = $this->getMockBuilder('PSX\Dispatch\CommandFactoryInterface')
			->setMethods(array('getCommand'))
			->getMock();

		$factory->expects($this->once())
			->method('getCommand')
			->with($this->identicalTo('Foo\Bar'))
			->will($this->returnValue($command));

		$executor = new Executor($factory, new Void(), getContainer()->get('event_dispatcher'));
		$executor->run(new Map('Foo\Bar', array('o' => 'bar', 'f' => true)));
	}

	public function testRunMissingOptionalParameter()
	{
		$testCase = $this;
		$command = new TestCommand(function(Parameters $parameters) use ($testCase){

			$testCase->assertTrue($parameters->has('r'));
			$testCase->assertEquals('foo', $parameters->get('r'));
			$testCase->assertFalse($parameters->has('o'));
			$testCase->assertEquals(null, $parameters->get('o'));
			$testCase->assertTrue($parameters->has('f'));
			$testCase->assertEquals(true, $parameters->get('f'));

		});

		$factory = $this->getMockBuilder('PSX\Dispatch\CommandFactoryInterface')
			->setMethods(array('getCommand'))
			->getMock();

		$factory->expects($this->once())
			->method('getCommand')
			->with($this->identicalTo('Foo\Bar'))
			->will($this->returnValue($command));

		$executor = new Executor($factory, new Void(), getContainer()->get('event_dispatcher'));
		$executor->run(new Map('Foo\Bar', array('r' => 'foo', 'f' => true)));
	}

	public function testRunMissingFlagParameter()
	{
		$testCase = $this;
		$command = new TestCommand(function(Parameters $parameters) use ($testCase){

			$testCase->assertTrue($parameters->has('r'));
			$testCase->assertEquals('foo', $parameters->get('r'));
			$testCase->assertTrue($parameters->has('o'));
			$testCase->assertEquals('bar', $parameters->get('o'));
			$testCase->assertFalse($parameters->has('f'));
			$testCase->assertEquals(false, $parameters->get('f'));

		});

		$factory = $this->getMockBuilder('PSX\Dispatch\CommandFactoryInterface')
			->setMethods(array('getCommand'))
			->getMock();

		$factory->expects($this->once())
			->method('getCommand')
			->with($this->identicalTo('Foo\Bar'))
			->will($this->returnValue($command));

		$executor = new Executor($factory, new Void(), getContainer()->get('event_dispatcher'));
		$executor->run(new Map('Foo\Bar', array('r' => 'foo', 'o' => 'bar')));
	}

	public function testAlias()
	{
		$command = new TestCommand(function(Parameters $parameters){
		});

		$factory = $this->getMockBuilder('PSX\Dispatch\CommandFactoryInterface')
			->setMethods(array('getCommand'))
			->getMock();

		$factory->expects($this->once())
			->method('getCommand')
			->with($this->identicalTo('Foo\Bar'))
			->will($this->returnValue($command));

		$executor = new Executor($factory, new Void(), getContainer()->get('event_dispatcher'));
		$executor->addAlias('foo', 'Foo\Bar');
		$executor->run(new Map('foo', array('r' => 'foo')));

		$this->assertEquals('bar', $executor->getClassName('bar'));
		$this->assertEquals('Foo\Bar', $executor->getClassName('foo'));
		$this->assertEquals(array('foo' => 'Foo\Bar'), $executor->getAliases());
	}

	public function testErrorCommand()
	{
		$command = new TestCommand(function(Parameters $parameters){
			throw new \Exception('foo');
		});

		$factory = $this->getMockBuilder('PSX\Dispatch\CommandFactoryInterface')
			->setMethods(array('getCommand'))
			->getMock();

		$factory->expects($this->at(0))
			->method('getCommand')
			->with($this->identicalTo('Foo\Bar'))
			->will($this->returnValue($command));

		$factory->expects($this->at(1))
			->method('getCommand')
			->with($this->identicalTo('PSX\Command\ErrorCommand'))
			->will($this->returnValue(getContainer()->get('command_factory')->getCommand('PSX\Command\ErrorCommand', new Location())));

		$executor = new Executor($factory, new Void(), getContainer()->get('event_dispatcher'));
		$executor->run(new Map('Foo\Bar', array('r' => 'foo')));
	}
}

class TestCommand extends CommandAbstract
{
	protected $callback;

	public function __construct(\Closure $callback)
	{
		$this->callback = $callback;
	}

	public function onExecute(Parameters $parameters, OutputInterface $output)
	{
		call_user_func($this->callback, $parameters);
	}

	public function getParameters()
	{
		return $this->getParameterBuilder()
			->addOption('r', Parameter::TYPE_REQUIRED)
			->addOption('o', Parameter::TYPE_OPTIONAL)
			->addOption('f', Parameter::TYPE_FLAG)
			->getParameters();
	}
}
