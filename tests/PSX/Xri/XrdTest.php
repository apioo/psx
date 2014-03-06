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

namespace PSX\Xri;

/**
 * XrdTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class XrdTest extends \PHPUnit_Framework_TestCase
{
	public function testParseXrds()
	{
		$xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<xrds:XRDS
    xmlns:xrds="xri://$xrds"
    xmlns:openid="http://openid.net/xmlns/1.0"
    xmlns="xri://$xrd*($v*2.0)">
  <XRD version="2.0">
    <Service priority="0">
      <Type>http://specs.openid.net/auth/2.0/signon</Type>
        <Type>http://openid.net/sreg/1.0</Type>
        <Type>http://openid.net/extensions/sreg/1.1</Type>
        <Type>http://schemas.openid.net/pape/policies/2007/06/phishing-resistant</Type>
        <Type>http://openid.net/srv/ax/1.0</Type>
      <URI>http://www.myopenid.com/server</URI>
      <LocalID>http://k42b3.myopenid.com/</LocalID>
    </Service>
    <Service priority="1">
      <Type>http://openid.net/signon/1.1</Type>
        <Type>http://openid.net/sreg/1.0</Type>
        <Type>http://openid.net/extensions/sreg/1.1</Type>
        <Type>http://schemas.openid.net/pape/policies/2007/06/phishing-resistant</Type>
        <Type>http://openid.net/srv/ax/1.0</Type>
      <URI>http://www.myopenid.com/server</URI>
      <openid:Delegate>http://k42b3.myopenid.com/</openid:Delegate>
    </Service>
    <Service priority="2">
      <Type>http://openid.net/signon/1.0</Type>
        <Type>http://openid.net/sreg/1.0</Type>
        <Type>http://openid.net/extensions/sreg/1.1</Type>
        <Type>http://schemas.openid.net/pape/policies/2007/06/phishing-resistant</Type>
        <Type>http://openid.net/srv/ax/1.0</Type>
      <URI>http://www.myopenid.com/server</URI>
      <openid:Delegate>http://k42b3.myopenid.com/</openid:Delegate>
    </Service>
  </XRD>
</xrds:XRDS>
XML;

		$xrd = Xrd::fromXrds(simplexml_load_string($xml));

		$this->assertEquals(true, is_array($xrd->getService()));
		$this->assertEquals(3, count($xrd->getService()));

		$services = $xrd->getService();

		$this->assertEquals(array('http://specs.openid.net/auth/2.0/signon', 'http://openid.net/sreg/1.0', 'http://openid.net/extensions/sreg/1.1', 'http://schemas.openid.net/pape/policies/2007/06/phishing-resistant', 'http://openid.net/srv/ax/1.0'), $services[0]->getType());
		$this->assertEquals('http://www.myopenid.com/server', $services[0]->getUri());
		$this->assertEquals('http://k42b3.myopenid.com/', $services[0]->getLocalId());

		$this->assertEquals(array('http://openid.net/signon/1.1', 'http://openid.net/sreg/1.0', 'http://openid.net/extensions/sreg/1.1', 'http://schemas.openid.net/pape/policies/2007/06/phishing-resistant', 'http://openid.net/srv/ax/1.0'), $services[1]->getType());
		$this->assertEquals('http://www.myopenid.com/server', $services[1]->getUri());

		$this->assertEquals(array('http://openid.net/signon/1.0', 'http://openid.net/sreg/1.0', 'http://openid.net/extensions/sreg/1.1', 'http://schemas.openid.net/pape/policies/2007/06/phishing-resistant', 'http://openid.net/srv/ax/1.0'), $services[2]->getType());
		$this->assertEquals('http://www.myopenid.com/server', $services[2]->getUri());
		$this->assertEquals(true, $services[2]->hasType('http://openid.net/extensions/sreg/1.1'));

		$service = $xrd->getServiceByType('http://specs.openid.net/auth/2.0/signon');

		$this->assertInstanceOf('PSX\Xri\Xrd\Service', $service);
		$this->assertEquals(array('http://specs.openid.net/auth/2.0/signon', 'http://openid.net/sreg/1.0', 'http://openid.net/extensions/sreg/1.1', 'http://schemas.openid.net/pape/policies/2007/06/phishing-resistant', 'http://openid.net/srv/ax/1.0'), $service->getType());
		$this->assertEquals('http://www.myopenid.com/server', $service->getUri());
		$this->assertEquals('http://k42b3.myopenid.com/', $service->getLocalId());
	}

	public function testParseOasisSpecXrds()
	{
		$xml = <<<'XML'
<XRDS xmlns="xri://$xrds" ref="xri://(tel:+1-201-555-0123)*foo">
    <XRD xmlns="xri://$xrd*($v*2.0)" version="2.0">
        <Query>*foo</Query>
        <Status code="100"/>
        <ServerStatus code="100"/>
        <Expires>2005-05-30T09:30:10Z</Expires>
        <ProviderID>xri://(tel:+1-201-555-0123)</ProviderID>
        <LocalID>*baz</LocalID>
        <EquivID>https://example.com/example/resource/</EquivID>
        <CanonicalID>xri://(tel:+1-201-555-0123)!1234</CanonicalID>
        <CanonicalEquivID>
         xri://=!4a76!c2f7!9033.78bd
        </CanonicalEquivID>
        <Service>
            <ProviderID>
             xri://(tel:+1-201-555-0123)!1234
            </ProviderID>
            <Type>xri://$res*auth*($v*2.0)</Type>
            <MediaType>application/xrds+xml</MediaType>
            <URI priority="10">http://resolve.example.com</URI>
            <URI priority="15">http://resolve2.example.com</URI>
            <URI>https://resolve.example.com</URI>
        </Service>
        <Service>
            <ProviderID>
             xri://(tel:+1-201-555-0123)!1234
            </ProviderID>
            <Type>xri://$res*auth*($v*2.0)</Type>
            <MediaType>application/xrds+xml;https=true</MediaType>
            <URI>https://resolve.example.com</URI>
        </Service>
        <Service>
            <Type match="null" />
            <Path select="true">/media/pictures</Path>
            <MediaType select="true">image/jpeg</MediaType>
            <URI append="path" >http://pictures.example.com</URI>
        </Service>
        <Service>
            <Type match="null" />
            <Path select="true">/media/videos</Path>
            <MediaType select="true">video/mpeg</MediaType>
            <URI append="path" >http://videos.example.com</URI>
        </Service>
        <Service>
            <ProviderID> xri://!!1000!1234.5678</ProviderID>
            <Type match="null" />
            <Path match="default" />
            <URI>http://example.com/local</URI>
        </Service>
        <Service>
            <Type>http://example.com/some/service/v3.1</Type>
            <URI>http://example.com/some/service/endpoint</URI>
            <LocalID>https://example.com/example/resource/</LocalID>
        </Service>
    </XRD>
</XRDS>
XML;

		$xrd = Xrd::fromXrds(simplexml_load_string($xml));

		$this->assertEquals('*foo', $xrd->getQuery());
		$this->assertEquals('100', $xrd->getStatus()->getCode());
		$this->assertEquals('100', $xrd->getServerStatus()->getCode());
		$this->assertEquals('Mon, 30 May 2005 09:30:10 +0000', $xrd->getExpires()->format('r'));
		$this->assertEquals('xri://(tel:+1-201-555-0123)', $xrd->getProviderId());
		$this->assertEquals('*baz', $xrd->getLocalId());
		$this->assertEquals('https://example.com/example/resource/', $xrd->getEquivId());
		$this->assertEquals('xri://(tel:+1-201-555-0123)!1234', $xrd->getCanonicalId());
		$this->assertEquals('xri://=!4a76!c2f7!9033.78bd', $xrd->getCanonicalEquivId());

		$services = $xrd->getService();

		$this->assertEquals('xri://(tel:+1-201-555-0123)!1234', $services[0]->getProviderId());
		$this->assertEquals(array('xri://$res*auth*($v*2.0)'), $services[0]->getType());
		$this->assertEquals('application/xrds+xml', $services[0]->getMediaType());
		$this->assertEquals('http://resolve.example.com', $services[0]->getUri());
		$this->assertEquals(array('http://resolve.example.com', 'http://resolve2.example.com', 'https://resolve.example.com'), $services[0]->getUris());

		$this->assertEquals('xri://(tel:+1-201-555-0123)!1234', $services[1]->getProviderId());
		$this->assertEquals(array('xri://$res*auth*($v*2.0)'), $services[1]->getType());
		$this->assertEquals('application/xrds+xml;https=true', $services[1]->getMediaType());
		$this->assertEquals('https://resolve.example.com', $services[1]->getUri());

		$this->assertEquals('/media/pictures', $services[2]->getPath());
		$this->assertEquals('image/jpeg', $services[2]->getMediaType());
		$this->assertEquals('http://pictures.example.com', $services[2]->getUri());

		$this->assertEquals('/media/videos', $services[3]->getPath());
		$this->assertEquals('video/mpeg', $services[3]->getMediaType());
		$this->assertEquals('http://videos.example.com', $services[3]->getUri());

		$this->assertEquals('xri://!!1000!1234.5678', $services[4]->getProviderId());
		$this->assertEquals('http://example.com/local', $services[4]->getUri());

		$this->assertEquals(array('http://example.com/some/service/v3.1'), $services[5]->getType());
		$this->assertEquals('http://example.com/some/service/endpoint', $services[5]->getUri());
		$this->assertEquals('https://example.com/example/resource/', $services[5]->getLocalId());
		$this->assertEquals(true, $services[5]->hasType('http://example.com/some/service/v3.1'));

		$service = $xrd->getServiceByType('http://example.com/some/service/v3.1');

		$this->assertInstanceOf('PSX\Xri\Xrd\Service', $service);
		$this->assertEquals(array('http://example.com/some/service/v3.1'), $service->getType());
		$this->assertEquals('http://example.com/some/service/endpoint', $service->getUri());
		$this->assertEquals('https://example.com/example/resource/', $service->getLocalId());
		$this->assertEquals(true, $service->hasType('http://example.com/some/service/v3.1'));
	}
}
