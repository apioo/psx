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

namespace PSX\Api\View\Generator;

use PSX\Api\View;

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
		$generator = new Xsd('http://foo.phpsx.org');
		$xsd       = $generator->generate($this->getView());

		$expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:tns="http://foo.phpsx.org" targetNamespace="http://foo.phpsx.org" elementFormDefault="qualified">
	<xs:element name="getResponse">
		<xs:complexType>
			<xs:sequence>
				<xs:element name="entry" type="tns:type7738db4616810154ab42db61b65f74aa" minOccurs="0" maxOccurs="unbounded"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:complexType name="type7738db4616810154ab42db61b65f74aa">
		<xs:sequence>
			<xs:element name="id" type="xs:integer" minOccurs="0" maxOccurs="1"/>
			<xs:element name="userId" type="xs:integer" minOccurs="0" maxOccurs="1"/>
			<xs:element name="title" type="tns:type3a2f0337802e500f838d048e0887d7bb" minOccurs="0" maxOccurs="1"/>
			<xs:element name="date" type="xs:dateTime" minOccurs="0" maxOccurs="1"/>
		</xs:sequence>
	</xs:complexType>
	<xs:simpleType name="type3a2f0337802e500f838d048e0887d7bb">
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
				<xs:element name="title" type="tns:type54c3770f40b7eb1d79765a48c83ab29b" minOccurs="1" maxOccurs="1"/>
				<xs:element name="date" type="xs:dateTime" minOccurs="1" maxOccurs="1"/>
			</xs:sequence>
		</xs:complexType>
	</xs:element>
	<xs:simpleType name="type54c3770f40b7eb1d79765a48c83ab29b">
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
				<xs:element name="title" type="tns:type3a2f0337802e500f838d048e0887d7bb" minOccurs="0" maxOccurs="1"/>
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
				<xs:element name="title" type="tns:type3a2f0337802e500f838d048e0887d7bb" minOccurs="0" maxOccurs="1"/>
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
	<xs:element name="getRequest" type="tns:void"/>
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
	<xs:element name="exceptionRecord" type="tns:fault"/>
</xs:schema>
XML;

		$this->assertXmlStringEqualsXmlString($expect, $xsd);
	}

	public function testXsdSchema()
	{
		$generator = new Xsd('http://ns.foo.com');
		$result    = $generator->generate($this->getView());

		$dom = new \DOMDocument();
		$dom->loadXML($result);

		$this->assertTrue($dom->schemaValidate(__DIR__ . '/Wsdl/schema.xsd'));
	}
}
