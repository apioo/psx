<?php

namespace PSX\Project\Tests\Api\Proxy;

use PSX\Project\Tests\ApiTestCase;

class SoapTest extends ApiTestCase
{
    public function testGet()
    {
        $response = $this->sendRequest('http://127.0.0.1/proxy/soap', 'GET', ['SOAPAction' => '/population/annotation/2#GET']);

        $body = (string) $response->getBody();
        $body = preg_replace('/<faultstring>(.*)<\/faultstring>/imsU', '<faultstring>[faultstring]</faultstring>', $body);
        $body = preg_replace('/<message>(.*)<\/message>/imsU', '<message>[message]</message>', $body);
        $body = preg_replace('/<trace>(.*)<\/trace>/imsU', '<trace>[trace]</trace>', $body);
        $body = preg_replace('/<context>(.*)<\/context>/imsU', '<context>[context]</context>', $body);

        $expect = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
 <soap:Body>
  <soap:Fault>
   <faultcode>soap:Server</faultcode>
   <faultstring>[faultstring]</faultstring>
   <detail>
    <error xmlns="http://phpsx.org/2014/data">
     <success>false</success>
     <title>PSX\Http\Exception\MethodNotAllowedException</title>
     <message>[message]</message>
     <trace>[trace]</trace>
     <context>[context]</context>
    </error>
   </detail>
  </soap:Fault>
 </soap:Body>
</soap:Envelope>
XML;

        $this->assertEquals(405, $response->getStatusCode(), $body);
        $this->assertXmlStringEqualsXmlString($expect, $body, $body);
    }

    public function testPost()
    {
        $response = $this->sendRequest('http://127.0.0.1/proxy/soap', 'POST', ['SOAPAction' => '/population/annotation/2#GET']);

        $body   = (string) $response->getBody();
        $expect = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
 <soap:Body>
  <getResponse xmlns="http://phpsx.org/2014/data">
   <id>2</id>
   <place>2</place>
   <region>United States</region>
   <population>307212123</population>
   <users>227719000</users>
   <world_users>13.1</world_users>
   <datetime>2009-11-29T15:22:40Z</datetime>
  </getResponse>
 </soap:Body>
</soap:Envelope>
XML;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertXmlStringEqualsXmlString($expect, $body, $body);
    }
}
