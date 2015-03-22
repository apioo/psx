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

namespace PSX\Test;

use Monolog\Logger;
use Monolog\Handler\NullHandler;
use PSX\Cache;
use PSX\Cache\Handler\Null as CacheHandler;
use PSX\Command\Output\Void;
use PSX\Dispatch\Sender\Void as VoidSender;
use PSX\Event;
use PSX\Event\ExceptionThrownEvent;
use PSX\Loader\RoutingParser;

/**
 * ContainerTestCaseTrait
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
trait ContainerTestCaseTrait
{
	protected $_protectedServices = array('connection');

	protected function setUp()
	{
		parent::setUp();

		// we remove all used services so that our test has no side effects
		$serviceIds = getContainer()->getServiceIds();
		foreach($serviceIds as $serviceId)
		{
			if(!in_array($serviceId, $this->_protectedServices))
			{
				getContainer()->set($serviceId, null);
			}
		}

		// set void logger
		$logger = new Logger('psx');
		$logger->pushHandler(new NullHandler());

		getContainer()->set('logger', $logger);

		// we replace the routing parser
		getContainer()->set('routing_parser', new RoutingParser\ArrayCollection($this->getPaths()));

		// assign the phpunit test case
		getContainer()->set('test_case', $this);

		// use null cache
		getContainer()->set('cache', new Cache(new CacheHandler()));

		// use void sender
		getContainer()->set('dispatch_sender', new VoidSender());

		// enables us to load the same controller multiple times
		getContainer()->get('loader')->setRecursiveLoading(true);

		// we replace the command output
		getContainer()->set('command_output', new Void());

		// add event listener which redirects PHPUnit exceptions
		$eventDispatcher = getContainer()->get('event_dispatcher');
		$eventDispatcher->addListener(Event::EXCEPTION_THROWN, function(ExceptionThrownEvent $event){

			if($event->getException() instanceof \PHPUnit_Framework_Exception)
			{
				throw $event->getException();
			}

		});
	}

	protected function tearDown()
	{
		parent::tearDown();

		// we remove all used services so that our test has no side effects
		$serviceIds = getContainer()->getServiceIds();
		foreach($serviceIds as $serviceId)
		{
			if(!in_array($serviceId, $this->_protectedServices))
			{
				getContainer()->set($serviceId, null);
			}
		}
	}
}
