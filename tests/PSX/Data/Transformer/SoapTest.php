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

namespace PSX\Data\Transformer;

/**
 * SoapTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SoapTest extends \PHPUnit_Framework_TestCase
{
	public function testTransform()
	{
		$body = <<<INPUT
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
		<test xmlns="http://phpsx.org/2014/data">
			<foo>bar</foo>
			<bar>blub</bar>
			<bar>bla</bar>
			<test>
				<foo>bar</foo>
			</test>
		</test>
	</soap:Body>
</soap:Envelope>
INPUT;

		$dom = new \DOMDocument();
		$dom->loadXML($body);

		$transformer = new Soap('http://phpsx.org/2014/data');

		$expect = array(
			'foo' => 'bar', 
			'bar' => array('blub', 'bla'), 
			'test' => array('foo' => 'bar'),
		);

		$data = $transformer->transform($dom);

		$this->assertTrue(is_array($data));
		$this->assertEquals($expect, $data);
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testNoEnvelope()
	{
		$body = <<<INPUT
<test xmlns="http://phpsx.org/2014/data">
	<foo>bar</foo>
	<bar>blub</bar>
	<bar>bla</bar>
	<test>
		<foo>bar</foo>
	</test>
</test>
INPUT;

		$dom = new \DOMDocument();
		$dom->loadXML($body);

		$transformer = new Soap('http://phpsx.org/2014/data');
		$transformer->transform($dom);
	}

	public function testEmptyBody()
	{
		$body = <<<INPUT
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
	<soap:Body>
	</soap:Body>
</soap:Envelope>
INPUT;

		$dom = new \DOMDocument();
		$dom->loadXML($body);

		$transformer = new Soap('http://phpsx.org/2014/data');

		$expect = array();

		$data = $transformer->transform($dom);

		$this->assertTrue(is_array($data));
		$this->assertEquals($expect, $data);
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function testBodyWrongNamespace()
	{
		$body = <<<INPUT
<soap:Envelope xmlns:soap="http://www.w3.org/2001/12/soap-envelope">
	<soap:Body>
	</soap:Body>
</soap:Envelope>
INPUT;

		$dom = new \DOMDocument();
		$dom->loadXML($body);

		$transformer = new Soap('http://phpsx.org/2014/data');
		$transformer->transform($dom);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testInvalidData()
	{
		$transformer = new Soap('http://phpsx.org/2014/data');
		$transformer->transform(array());
	}
}
