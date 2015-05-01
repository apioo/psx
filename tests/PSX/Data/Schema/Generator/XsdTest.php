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

namespace PSX\Data\Schema\Generator;

/**
 * XsdTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
			<xs:annotation>
				<xs:documentation>An general news entry</xs:documentation>
			</xs:annotation>
			<xs:sequence>
				<xs:element name="tags" type="xs:string" maxOccurs="6" minOccurs="1"/>
				<xs:element name="receiver" type="tns:type6039818cbcfdcc20777fd8c74a5a392d" maxOccurs="unbounded" minOccurs="1"/>
				<xs:element name="read" type="xs:boolean" minOccurs="0" maxOccurs="1"/>
				<xs:element name="author" type="tns:type6039818cbcfdcc20777fd8c74a5a392d" minOccurs="1" maxOccurs="1"/>
				<xs:element name="sendDate" type="xs:date" minOccurs="0" maxOccurs="1"/>
				<xs:element name="readDate" type="xs:dateTime" minOccurs="0" maxOccurs="1"/>
				<xs:element name="expires" type="xs:duration" minOccurs="0" maxOccurs="1"/>
				<xs:element name="price" type="tns:type4e5a1e85b79b2425597aeb1990a51410" minOccurs="1" maxOccurs="1"/>
				<xs:element name="rating" type="tns:typeaa8f7b0d0f2d6a79673522eae0046721" minOccurs="0" maxOccurs="1"/>
				<xs:element name="content" type="tns:type3837f63ebc956e53467602abf362bbab" minOccurs="1" maxOccurs="1"/>
				<xs:element name="question" type="tns:type6baf15f8d23525a89c7f91826e7a2933" minOccurs="0" maxOccurs="1"/>
				<xs:element name="coffeeTime" type="xs:time" minOccurs="0" maxOccurs="1"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:complexType name="type6039818cbcfdcc20777fd8c74a5a392d">
		<xs:annotation>
			<xs:documentation>An simple author element with some description</xs:documentation>
		</xs:annotation>
		<xs:sequence>
			<xs:element maxOccurs="1" minOccurs="1" name="title" type="tns:type6e27a21415fe6682ada7da65eed778eb"/>
			<xs:element name="email" type="xs:string" minOccurs="0" maxOccurs="1"/>
			<xs:element maxOccurs="8" minOccurs="0" name="categories" type="xs:string"/>
			<xs:element maxOccurs="unbounded" minOccurs="0" name="locations" type="tns:typee081a664cb5227a334bc5e0fa367f178"/>
			<xs:element maxOccurs="1" minOccurs="0" name="origin" type="tns:typee081a664cb5227a334bc5e0fa367f178"/>
		</xs:sequence>
	</xs:complexType>
	<xs:simpleType name="type6e27a21415fe6682ada7da65eed778eb">
		<xs:restriction base="xs:string">
			<xs:pattern value="[A-z]{3,16}"/>
		</xs:restriction>
	</xs:simpleType>
	<xs:complexType name="typee081a664cb5227a334bc5e0fa367f178">
		<xs:annotation>
			<xs:documentation>Location of the person</xs:documentation>
		</xs:annotation>
		<xs:sequence>
			<xs:element maxOccurs="1" minOccurs="0" name="lat" type="xs:integer"/>
			<xs:element maxOccurs="1" minOccurs="0" name="long" type="xs:integer"/>
		</xs:sequence>
	</xs:complexType>
	<xs:simpleType name="type4e5a1e85b79b2425597aeb1990a51410">
		<xs:restriction base="xs:float">
			<xs:maxInclusive value="100"/>
			<xs:minInclusive value="1"/>
		</xs:restriction>
	</xs:simpleType>
	<xs:simpleType name="typeaa8f7b0d0f2d6a79673522eae0046721">
		<xs:restriction base="xs:integer">
			<xs:maxInclusive value="5"/>
			<xs:minInclusive value="1"/>
		</xs:restriction>
	</xs:simpleType>
	<xs:simpleType name="type3837f63ebc956e53467602abf362bbab">
		<xs:annotation>
			<xs:documentation>Contains the main content of the news entry</xs:documentation>
		</xs:annotation>
		<xs:restriction base="xs:string">
			<xs:minLength value="3"/>
			<xs:maxLength value="512"/>
		</xs:restriction>
	</xs:simpleType>
	<xs:simpleType name="type6baf15f8d23525a89c7f91826e7a2933">
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

		$this->assertTrue($dom->schemaValidate(__DIR__ . '/../../../Api/Resource/Generator/Wsdl/schema.xsd'));
	}
}
