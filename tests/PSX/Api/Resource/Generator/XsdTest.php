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

namespace PSX\Api\Resource\Generator;

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
		$generator = new Xsd('http://foo.phpsx.org');
		$xsd       = $generator->generate($this->getResource());

		$expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:tns="http://foo.phpsx.org" targetNamespace="http://foo.phpsx.org" elementFormDefault="qualified">
	<xs:element name="getRequest" type="tns:void"/>
	<xs:element name="getResponse">
		<xs:complexType>
			<xs:sequence>
				<xs:element name="entry" type="tns:type993f4bb37f524889fc963fedd6381458" minOccurs="0" maxOccurs="unbounded"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:complexType name="type993f4bb37f524889fc963fedd6381458">
		<xs:sequence>
			<xs:element name="id" type="xs:integer" minOccurs="0" maxOccurs="1"/>
			<xs:element name="userId" type="xs:integer" minOccurs="0" maxOccurs="1"/>
			<xs:element name="title" type="tns:type69981114cb5d0f6f81cb98d20181187a" minOccurs="0" maxOccurs="1"/>
			<xs:element name="date" type="xs:dateTime" minOccurs="0" maxOccurs="1"/>
		</xs:sequence>
	</xs:complexType>
	<xs:simpleType name="type69981114cb5d0f6f81cb98d20181187a">
		<xs:restriction base="xs:string">
			<xs:minLength value="3"/>
			<xs:maxLength value="16"/>
			<xs:pattern value="[A-z]+"/>
		</xs:restriction>
	</xs:simpleType>
	<xs:element name="postRequest">
		<xs:complexType>
			<xs:sequence>
				<xs:element name="id" type="xs:integer" minOccurs="0" maxOccurs="1"/>
				<xs:element name="userId" type="xs:integer" minOccurs="0" maxOccurs="1"/>
				<xs:element name="title" type="tns:type25927bd8a7b80c1d8fc520de123ae19a" minOccurs="1" maxOccurs="1"/>
				<xs:element name="date" type="xs:dateTime" minOccurs="1" maxOccurs="1"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:simpleType name="type25927bd8a7b80c1d8fc520de123ae19a">
		<xs:restriction base="xs:string">
			<xs:minLength value="3"/>
			<xs:maxLength value="16"/>
			<xs:pattern value="[A-z]+"/>
		</xs:restriction>
	</xs:simpleType>
	<xs:element name="postResponse">
		<xs:complexType>
			<xs:sequence>
				<xs:element name="success" type="xs:boolean" minOccurs="0" maxOccurs="1"/>
				<xs:element name="message" type="xs:string" minOccurs="0" maxOccurs="1"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:element name="putRequest">
		<xs:complexType>
			<xs:sequence>
				<xs:element name="id" type="xs:integer" minOccurs="1" maxOccurs="1"/>
				<xs:element name="userId" type="xs:integer" minOccurs="0" maxOccurs="1"/>
				<xs:element name="title" type="tns:type69981114cb5d0f6f81cb98d20181187a" minOccurs="0" maxOccurs="1"/>
				<xs:element name="date" type="xs:dateTime" minOccurs="0" maxOccurs="1"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:element name="putResponse">
		<xs:complexType>
			<xs:sequence>
				<xs:element name="success" type="xs:boolean" minOccurs="0" maxOccurs="1"/>
				<xs:element name="message" type="xs:string" minOccurs="0" maxOccurs="1"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:element name="deleteRequest">
		<xs:complexType>
			<xs:sequence>
				<xs:element name="id" type="xs:integer" minOccurs="1" maxOccurs="1"/>
				<xs:element name="userId" type="xs:integer" minOccurs="0" maxOccurs="1"/>
				<xs:element name="title" type="tns:type69981114cb5d0f6f81cb98d20181187a" minOccurs="0" maxOccurs="1"/>
				<xs:element name="date" type="xs:dateTime" minOccurs="0" maxOccurs="1"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:element name="deleteResponse">
		<xs:complexType>
			<xs:sequence>
				<xs:element name="success" type="xs:boolean" minOccurs="0" maxOccurs="1"/>
				<xs:element name="message" type="xs:string" minOccurs="0" maxOccurs="1"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:complexType name="fault">
		<xs:sequence>
			<xs:element name="success" type="xs:boolean" minOccurs="1" maxOccurs="1"/>
			<xs:element name="title" type="xs:string" minOccurs="0" maxOccurs="1"/>
			<xs:element name="message" type="xs:string" minOccurs="1" maxOccurs="1"/>
			<xs:element name="trace" type="xs:string" minOccurs="0" maxOccurs="1"/>
			<xs:element name="context" type="xs:string" minOccurs="0" maxOccurs="1"/>
		</xs:sequence>
	</xs:complexType>
	<xs:complexType name="void">
		<xs:sequence/>
	</xs:complexType>
	<xs:element name="error" type="tns:fault"/>
</xs:schema>
XML;

		$this->assertXmlStringEqualsXmlString($expect, $xsd);
	}

	public function testXsdSchema()
	{
		$generator = new Xsd('http://ns.foo.com');
		$result    = $generator->generate($this->getResource());

		$dom = new \DOMDocument();
		$dom->loadXML($result);

		$this->assertTrue($dom->schemaValidate(__DIR__ . '/Wsdl/schema.xsd'));
	}
}
