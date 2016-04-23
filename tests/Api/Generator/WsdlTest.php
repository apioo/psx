<?php

namespace PSX\Project\Tests\Api\Generator;

use PSX\Project\Tests\ApiTestCase;

class WsdlTest extends ApiTestCase
{
    public function testGet()
    {
        $response = $this->sendRequest('http://127.0.0.1/generator/wsdl/*/population/popo', 'GET');

        $body   = (string) $response->getBody();
        $expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<wsdl:definitions xmlns:xs="http://www.w3.org/2001/XMLSchema" name="Population" targetNamespace="http://phpsx.org/2014/data" xmlns:tns="http://phpsx.org/2014/data" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/">
  <wsdl:types xmlns:xs="http://www.w3.org/2001/XMLSchema">
    <xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema" targetNamespace="http://phpsx.org/2014/data" elementFormDefault="qualified" xmlns:tns="http://phpsx.org/2014/data">
      <xs:element name="getRequest" type="tns:void"/>
      <xs:element name="getResponse">
        <xs:complexType>
          <xs:annotation>
            <xs:documentation>Collection result</xs:documentation>
          </xs:annotation>
          <xs:sequence>
            <xs:element name="totalResults" type="xs:integer" minOccurs="0" maxOccurs="1"/>
            <xs:element name="entry" type="tns:type4fe78e9f8d9266767f15f9b094d00e9d" minOccurs="0" maxOccurs="unbounded"/>
          </xs:sequence>
        </xs:complexType>
      </xs:element>
      <xs:complexType name="type4fe78e9f8d9266767f15f9b094d00e9d">
        <xs:annotation>
          <xs:documentation>Represents an internet population entity</xs:documentation>
        </xs:annotation>
        <xs:sequence>
          <xs:element name="id" type="xs:integer" minOccurs="0" maxOccurs="1"/>
          <xs:element name="place" type="xs:integer" minOccurs="1" maxOccurs="1"/>
          <xs:element name="region" type="tns:type223a14ad48026b2ee7c4dcf2b0d4c934" minOccurs="1" maxOccurs="1"/>
          <xs:element name="population" type="xs:integer" minOccurs="1" maxOccurs="1"/>
          <xs:element name="users" type="xs:integer" minOccurs="1" maxOccurs="1"/>
          <xs:element name="worldUsers" type="xs:float" minOccurs="1" maxOccurs="1"/>
          <xs:element name="datetime" type="xs:dateTime" minOccurs="0" maxOccurs="1"/>
        </xs:sequence>
      </xs:complexType>
      <xs:simpleType name="type223a14ad48026b2ee7c4dcf2b0d4c934">
        <xs:annotation>
          <xs:documentation>Name of the region</xs:documentation>
        </xs:annotation>
        <xs:restriction base="xs:string">
          <xs:minLength value="3"/>
          <xs:maxLength value="64"/>
          <xs:pattern value="[A-z]+"/>
        </xs:restriction>
      </xs:simpleType>
      <xs:element name="postRequest">
        <xs:complexType>
          <xs:annotation>
            <xs:documentation>Represents an internet population entity</xs:documentation>
          </xs:annotation>
          <xs:sequence>
            <xs:element name="id" type="xs:integer" minOccurs="0" maxOccurs="1"/>
            <xs:element name="place" type="xs:integer" minOccurs="1" maxOccurs="1"/>
            <xs:element name="region" type="tns:type223a14ad48026b2ee7c4dcf2b0d4c934" minOccurs="1" maxOccurs="1"/>
            <xs:element name="population" type="xs:integer" minOccurs="1" maxOccurs="1"/>
            <xs:element name="users" type="xs:integer" minOccurs="1" maxOccurs="1"/>
            <xs:element name="worldUsers" type="xs:float" minOccurs="1" maxOccurs="1"/>
            <xs:element name="datetime" type="xs:dateTime" minOccurs="0" maxOccurs="1"/>
          </xs:sequence>
        </xs:complexType>
      </xs:element>
      <xs:element name="postResponse">
        <xs:complexType>
          <xs:annotation>
            <xs:documentation>Operation message</xs:documentation>
          </xs:annotation>
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
  </wsdl:types>
  <wsdl:message name="getCollectionInput">
    <wsdl:part name="body" element="tns:getRequest"/>
  </wsdl:message>
  <wsdl:message name="getCollectionOutput">
    <wsdl:part name="body" element="tns:getResponse"/>
  </wsdl:message>
  <wsdl:message name="postEntityInput">
    <wsdl:part name="body" element="tns:postRequest"/>
  </wsdl:message>
  <wsdl:message name="postEntityOutput">
    <wsdl:part name="body" element="tns:postResponse"/>
  </wsdl:message>
  <wsdl:message name="faultOutput">
    <wsdl:part name="body" element="tns:error"/>
  </wsdl:message>
  <wsdl:portType name="PopulationPortType">
    <wsdl:operation name="getCollection">
      <wsdl:input message="tns:getCollectionInput"/>
      <wsdl:output message="tns:getCollectionOutput"/>
      <wsdl:fault message="tns:faultOutput" name="SoapFaultException"/>
    </wsdl:operation>
    <wsdl:operation name="postEntity">
      <wsdl:input message="tns:postEntityInput"/>
      <wsdl:output message="tns:postEntityOutput"/>
      <wsdl:fault message="tns:faultOutput" name="SoapFaultException"/>
    </wsdl:operation>
  </wsdl:portType>
  <wsdl:binding name="PopulationBinding" type="tns:PopulationPortType">
    <soap:binding style="document" transport="http://schemas.xmlsoap.org/soap/http"/>
    <wsdl:operation name="getCollection">
      <soap:operation soapAction="/population/popo#GET"/>
      <wsdl:input>
        <soap:body use="literal"/>
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal"/>
      </wsdl:output>
      <wsdl:fault name="SoapFaultException">
        <soap:body use="literal" name="SoapFaultException"/>
      </wsdl:fault>
    </wsdl:operation>
    <wsdl:operation name="postEntity">
      <soap:operation soapAction="/population/popo#POST"/>
      <wsdl:input>
        <soap:body use="literal"/>
      </wsdl:input>
      <wsdl:output>
        <soap:body use="literal"/>
      </wsdl:output>
      <wsdl:fault name="SoapFaultException">
        <soap:body use="literal" name="SoapFaultException"/>
      </wsdl:fault>
    </wsdl:operation>
  </wsdl:binding>
  <wsdl:service name="PopulationService">
    <wsdl:port name="PopulationPort" binding="tns:PopulationBinding">
      <soap:address location="http://127.0.0.1/proxy/soap"/>
    </wsdl:port>
  </wsdl:service>
</wsdl:definitions>
XML;

        $this->assertEquals(null, $response->getStatusCode(), $body);
        $this->assertXmlStringEqualsXmlString($expect, $body, $body);
    }
}
