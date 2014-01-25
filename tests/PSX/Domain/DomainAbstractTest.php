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

namespace PSX\Domain;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * DomainAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DomainAbstractTest extends \PHPUnit_Framework_TestCase
{
	public function testSetContainer()
	{
		$domain = new TestDomain();

		$this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerAwareInterface', $domain);

		$domain->setContainer(getContainer());
	}

	public function testGetService()
	{
		$domain = new TestDomain();
		$domain->setContainer(getContainer());

		$this->assertInstanceOf('PSX\Config', $domain->getConfig());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testGetInvalidService()
	{
		$domain = new TestDomain();
		$domain->setContainer(getContainer());
		$domain->getFoo();
	}

	public function testEventDispatch()
	{
		$domain = new TestDomain();
		$domain->setContainer(getContainer());

		$this->assertEmpty($domain->getListeners());
		$this->assertEmpty($domain->getListeners('foo'));

		$event    = new FooEvent();
		$listener = $this->getMock('PSX\Domain\TestListener', array('notify'));
		$listener->expects($this->once())
				->method('notify')
				->with($this->equalTo($event));

		$callback = array($listener, 'notify');

		$domain->addListener('foo', $callback);

		$this->assertTrue($domain->hasListeners('foo'));
		$this->assertEquals($callback, current($domain->getListeners('foo')));

		$domain->dispatch('foo', $event);
		$domain->removeListener('foo', $callback);

		$this->assertEmpty($domain->getListeners('foo'));
	}

	public function testEventSubscriber()
	{
		$domain = new TestDomain();
		$domain->setContainer(getContainer());

		$event      = new FooEvent();
		$subscriber = $this->getMock('PSX\Domain\TestSubscriber', array('notify'));
		$subscriber->expects($this->once())
				->method('notify')
				->with($this->equalTo($event));

		$domain->addSubscriber($subscriber);
		$domain->dispatch('foo', $event);
	}
}

class TestDomain extends DomainAbstract
{
}

class TestListener
{
	public function notify(FooEvent $event)
	{
	}
}

class TestSubscriber implements EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
		return array(
			'foo' => 'notify',
		);
	}

	public function notify(FooEvent $event)
	{
	}
}

class FooEvent extends Event
{
}
