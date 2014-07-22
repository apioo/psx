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

namespace PSX\Data\Schema\Generator;

/**
 * XsdTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class XsdTest extends GeneratorTestCase
{
	public function testGenerate()
	{
		$generator = new Xsd('http://ns.foo.com');
		$result    = $generator->generate($this->getSchema());

		$expect = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<xs:schema xmlns="http://ns.foo.com" xmlns:xs="http://www.w3.org/2001/XMLSchema" targetNamespace="http://ns.foo.com" elementFormDefault="qualified">
	<xs:element name="news">
		<xs:complexType>
			<xs:sequence>
				<xs:element name="tags" type="tags" minOccurs="0" maxOccurs="1"/>
				<xs:element name="receiver" type="receiver" minOccurs="1" maxOccurs="1"/>
				<xs:element name="read" type="xs:boolean" minOccurs="0" maxOccurs="1"/>
				<xs:element name="author" type="author" minOccurs="1" maxOccurs="1"/>
				<xs:element name="sendDate" type="xs:date" minOccurs="0" maxOccurs="1"/>
				<xs:element name="readDate" type="xs:dateTime" minOccurs="0" maxOccurs="1"/>
				<xs:element name="expires" type="xs:duration" minOccurs="0" maxOccurs="1"/>
				<xs:element name="price" type="price" minOccurs="1" maxOccurs="1"/>
				<xs:element name="rating" type="rating" minOccurs="0" maxOccurs="1"/>
				<xs:element name="content" type="content" minOccurs="1" maxOccurs="1"/>
				<xs:element name="question" type="question" minOccurs="0" maxOccurs="1"/>
				<xs:element name="coffeeTime" type="xs:time" minOccurs="0" maxOccurs="1"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:complexType name="tags">
		<xs:sequence>
			<xs:element name="tag" type="xs:string" minOccurs="1" maxOccurs="1"/>
		</xs:sequence>
	</xs:complexType>
	<xs:complexType name="receiver">
		<xs:sequence>
			<xs:element name="author" type="author" minOccurs="1" maxOccurs="1"/>
		</xs:sequence>
	</xs:complexType>
	<xs:complexType name="author">
		<xs:sequence>
			<xs:element name="title" type="title" minOccurs="1" maxOccurs="1"/>
			<xs:element name="email" type="xs:string" minOccurs="0" maxOccurs="1"/>
		</xs:sequence>
	</xs:complexType>
	<xs:simpleType name="title">
		<xs:restriction base="xs:string">
			<xs:pattern value="[A-z]{3,16}"/>
		</xs:restriction>
	</xs:simpleType>
	<xs:simpleType name="price">
		<xs:restriction base="xs:float">
			<xs:maxInclusive value="100"/>
			<xs:minInclusive value="1"/>
		</xs:restriction>
	</xs:simpleType>
	<xs:simpleType name="rating">
		<xs:restriction base="xs:integer">
			<xs:maxInclusive value="5"/>
			<xs:minInclusive value="1"/>
		</xs:restriction>
	</xs:simpleType>
	<xs:simpleType name="content">
		<xs:restriction base="xs:string">
			<xs:minLength value="3"/>
			<xs:maxLength value="512"/>
		</xs:restriction>
	</xs:simpleType>
	<xs:simpleType name="question">
		<xs:restriction base="xs:string">
			<xs:enumeration value="foo"/>
			<xs:enumeration value="bar"/>
		</xs:restriction>
	</xs:simpleType>
</xs:schema>
XML;

		$this->assertXmlStringEqualsXmlString($expect, $result);
	}

	/**
	 * Check whether the generated xsd is valid and we can use it agains some 
	 * custom xml
	 */
	public function testXsd()
	{
		$generator = new Xsd('http://ns.foo.com');
		$result    = $generator->generate($this->getSchema());

		$xml = <<<XML
<news xmlns="http://ns.foo.com">
	<tags>
		<tag>foo</tag>
	</tags>
	<receiver>
		<author>
			<title>bar</title>
		</author>
	</receiver>
	<read>1</read>
	<author>
		<title>test</title>
	</author>
	<sendDate>2014-07-22</sendDate>
	<readDate>2014-07-22T22:47:00</readDate>
	<expires>P1M</expires>
	<price>13.37</price>
	<rating>4</rating>
	<content>foobar</content>
	<coffeeTime>16:00:00</coffeeTime>
</news>
XML;

		$dom = new \DOMDocument();
		$dom->loadXML($xml);

		$this->assertTrue($dom->schemaValidateSource($result));
	}
}
