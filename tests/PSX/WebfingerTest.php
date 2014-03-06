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

use PSX\Webfinger\ResourceNotFoundException;
use PSX\Http\Handler\Mock;
use PSX\Http\Handler\MockCapture;

/**
 * WebfingerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class WebfingerTest extends \PHPUnit_Framework_TestCase
{
	private $http;
	private $webfinger;

	protected function setUp()
	{
		//$mockCapture = new MockCapture('tests/PSX/Webfinger/webfinger_http_fixture.xml');
		$mock = Mock::getByXmlDefinition('tests/PSX/Webfinger/webfinger_http_fixture.xml');

		$this->http      = new Http($mock);
		$this->webfinger = new Webfinger($this->http);
	}

	protected function tearDown()
	{
		unset($this->webfinger);
		unset($this->http);
	}

	/**
	 * Test discover from an google endpoint using hostmeta fallback discovery
	 */
	public function testDiscoverByEmailHostmeta()
	{
		$document = $this->webfinger->discoverByEmail('romeda@gmail.com');

		$this->assertInstanceOf('PSX\Hostmeta\DocumentAbstract', $document);

		$this->assertEquals('acct:romeda@gmail.com', $document->getSubject());
		$this->assertEquals(array('http://www.google.com/profiles/romeda'), $document->getAliases());

		$links = $document->getLinks();

		$this->assertEquals(8, count($links));

		$this->assertEquals('http://portablecontacts.net/spec/1.0', $links[0]->getRel());
		$this->assertEquals('http://www-opensocial.googleusercontent.com/api/people/', $links[0]->getHref());

		$this->assertEquals('http://portablecontacts.net/spec/1.0#me', $links[1]->getRel());
		$this->assertEquals('http://www-opensocial.googleusercontent.com/api/people/113651174506128852447/', $links[1]->getHref());

		$this->assertEquals('http://webfinger.net/rel/profile-page', $links[2]->getRel());
		$this->assertEquals('text/plain', $links[2]->getType());
		$this->assertEquals('http://www.google.com/profiles/romeda', $links[2]->getHref());
	}

	/**
	 * Test discover from an pump.io endpoint wich has an webfinger endpoint
	 */
	public function testDiscoverByEmailWebfinger()
	{
		$document = $this->webfinger->discoverByEmail('test@pumpity.net');

		$this->assertInstanceOf('PSX\Hostmeta\DocumentAbstract', $document);

		$links = $document->getLinks();

		$this->assertEquals(9, count($links));

		$this->assertEquals('http://webfinger.net/rel/profile-page', $links[0]->getRel());
		$this->assertEquals('text/html', $links[0]->getType());
		$this->assertEquals('https://pumpity.net/test', $links[0]->getHref());
	}

	/**
	 * This test simply discovers various identites to test service 
	 * interoperability
	 */
	public function testDiscoverVariousIdentities()
	{
		$identities = array(
			'evalpaul@gmail.com',
			// yahoo does not follow the spec properly when resolving a template 
			// the email must provided without acct: scheme also the xrd 
			// document gets served as text/plain
			//'mcorne@yahoo.com',
			'M4dSquirrels@aol.com', 
			'kevinkleinman@joindiaspora.com', 
			'test@pumpity.net', 
			'singpolyma@identi.ca', 
			'romeda@gmail.com',
		);

		foreach($identities as $identity)
		{
			$document = $this->webfinger->discoverByEmail($identity);

			$this->assertInstanceOf('PSX\Hostmeta\DocumentAbstract', $document);
		}
	}
}

