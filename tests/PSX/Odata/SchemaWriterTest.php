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

namespace PSX\Odata;

use PSX\OpenSocial\Data\Organization;

/**
 * SchemaWriterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SchemaWriterTest extends \PHPUnit_Framework_TestCase
{
	public function testWriter()
	{
		$record = new Organization();

		$writer = new SchemaWriter('psx');
		$writer->addEntityType($record);

		$actual   = $writer->toString();
		$expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<edmx:Edmx xmlns:edmx="http://schemas.microsoft.com/ado/2007/06/edmx" Version="1.0">
 <edmx:DataServices xmlns:m="http://schemas.microsoft.com/ado/2007/08/dataservices/metadata" m:DataServiceVersion="3.0" m:MaxDataServiceVersion="3.0">
  <Schema xmlns="http://schemas.microsoft.com/ado/2009/11/edm" Namespace="psx">
   <EntityType Name="organization">
    <Property Name="Address" Type="psx.address"/>
    <Property Name="Department" Type="Edm.String"/>
    <Property Name="Description" Type="Edm.String"/>
    <Property Name="EndDate" Type="Edm.String"/>
    <Property Name="Field" Type="Edm.String"/>
    <Property Name="Location" Type="Edm.String"/>
    <Property Name="Name" Type="Edm.String"/>
    <Property Name="Salary" Type="Edm.String"/>
    <Property Name="StartDate" Type="Edm.String"/>
    <Property Name="Subfield" Type="Edm.String"/>
    <Property Name="Title" Type="Edm.String"/>
    <Property Name="Type" Type="Edm.String"/>
    <Property Name="Webpage" Type="Edm.String"/>
    <ComplexType Name="Address">
     <Property Name="Country" Type="Edm.String"/>
     <Property Name="Formatted" Type="Edm.String"/>
     <Property Name="Latitude" Type="Edm.String"/>
     <Property Name="Locality" Type="Edm.String"/>
     <Property Name="Longitude" Type="Edm.String"/>
     <Property Name="PostalCode" Type="Edm.String"/>
     <Property Name="Region" Type="Edm.String"/>
     <Property Name="StreetAddress" Type="Edm.String"/>
     <Property Name="Type" Type="Edm.String"/>
    </ComplexType>
   </EntityType>
  </Schema>
 </edmx:DataServices>
</edmx:Edmx>
XML;

		$this->assertXmlStringEqualsXmlString($expected, $actual);
	}
}
