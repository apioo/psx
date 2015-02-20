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
<xs:schema xmlns:tns="http://ns.foo.com" xmlns:xs="http://www.w3.org/2001/XMLSchema" elementFormDefault="qualified" targetNamespace="http://ns.foo.com">
	<xs:element name="news">
		<xs:complexType>
			<xs:sequence>
				<xs:element name="tags" type="xs:string" maxOccurs="unbounded" minOccurs="1"/>
				<xs:element name="receiver" type="tns:typec4ddf063f76e992fb7401c8cb36ab534" maxOccurs="unbounded" minOccurs="1"/>
				<xs:element name="read" type="xs:boolean" minOccurs="0" maxOccurs="1"/>
				<xs:element name="author" type="tns:typec4ddf063f76e992fb7401c8cb36ab534" minOccurs="1" maxOccurs="1"/>
				<xs:element name="sendDate" type="xs:date" minOccurs="0" maxOccurs="1"/>
				<xs:element name="readDate" type="xs:dateTime" minOccurs="0" maxOccurs="1"/>
				<xs:element name="expires" type="xs:duration" minOccurs="0" maxOccurs="1"/>
				<xs:element name="price" type="tns:type41b5d91a7e5b6a356e679cc5fa5d64b6" minOccurs="1" maxOccurs="1"/>
				<xs:element name="rating" type="tns:type442ba5164a6db9bc4be656bccda23328" minOccurs="0" maxOccurs="1"/>
				<xs:element name="content" type="tns:type6022b25ec119c5585bd9109efee01a3e" minOccurs="1" maxOccurs="1"/>
				<xs:element name="question" type="tns:typeabbed36c306165ec45c99cbe9488a57f" minOccurs="0" maxOccurs="1"/>
				<xs:element name="coffeeTime" type="xs:time" minOccurs="0" maxOccurs="1"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:complexType name="typec4ddf063f76e992fb7401c8cb36ab534">
		<xs:sequence>
			<xs:element name="title" type="tns:typef385c15a0c06eeab4f4a007c40599064" minOccurs="1" maxOccurs="1"/>
			<xs:element name="email" type="xs:string" minOccurs="0" maxOccurs="1"/>
			<xs:element maxOccurs="unbounded" minOccurs="0" name="categories" type="xs:string"/>
			<xs:element maxOccurs="unbounded" minOccurs="0" name="locations" type="tns:typeb534788702d7583a85337e047716e924"/>
			<xs:element maxOccurs="1" minOccurs="0" name="origin" type="tns:typeb534788702d7583a85337e047716e924"/>
		</xs:sequence>
	</xs:complexType>
	<xs:simpleType name="typef385c15a0c06eeab4f4a007c40599064">
		<xs:restriction base="xs:string">
			<xs:pattern value="[A-z]{3,16}"/>
		</xs:restriction>
	</xs:simpleType>
	<xs:complexType name="typeb534788702d7583a85337e047716e924">
		<xs:sequence>
			<xs:element maxOccurs="1" minOccurs="0" name="lat" type="xs:integer"/>
			<xs:element maxOccurs="1" minOccurs="0" name="long" type="xs:integer"/>
		</xs:sequence>
	</xs:complexType>
	<xs:simpleType name="type41b5d91a7e5b6a356e679cc5fa5d64b6">
		<xs:restriction base="xs:float">
			<xs:maxInclusive value="100"/>
			<xs:minInclusive value="1"/>
		</xs:restriction>
	</xs:simpleType>
	<xs:simpleType name="type442ba5164a6db9bc4be656bccda23328">
		<xs:restriction base="xs:integer">
			<xs:maxInclusive value="5"/>
			<xs:minInclusive value="1"/>
		</xs:restriction>
	</xs:simpleType>
	<xs:simpleType name="type6022b25ec119c5585bd9109efee01a3e">
		<xs:restriction base="xs:string">
			<xs:minLength value="3"/>
			<xs:maxLength value="512"/>
		</xs:restriction>
	</xs:simpleType>
	<xs:simpleType name="typeabbed36c306165ec45c99cbe9488a57f">
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
	<tags>foo</tags>
	<tags>bar</tags>
	<receiver>
		<title>bar</title>
	</receiver>
	<read>1</read>
	<author>
		<title>test</title>
		<categories>foo</categories>
		<categories>bar</categories>
		<locations>
			<lat>13</lat>
			<long>-37</long>
		</locations>
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

	/**
	 * Test whether the generated XSD follows the schema XSD
	 */
	public function testXsdSchema()
	{
		$generator = new Xsd('http://ns.foo.com');
		$result    = $generator->generate($this->getSchema());

		$dom = new \DOMDocument();
		$dom->loadXML($result);

		$this->assertTrue($dom->schemaValidate(__DIR__ . '/../../../Wsdl/schema.xsd'));
	}
}
