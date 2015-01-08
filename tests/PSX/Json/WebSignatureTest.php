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

namespace PSX\Json;

/**
 * WebSignatureTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class WebSignatureTest extends \PHPUnit_Framework_TestCase
{
	public function testCreate()
	{
		// signing key
		$key = 'AyM1SysPpbyDfgZld3umj1qzKObwVMkoqQ-EstJQLr_T-1qS0gZH75aKtMN3Yj0iPS4hcgUuTwjAzZr1Z9CAow';

		// produce signature
		$token = new WebSignature();
		$token->setHeader(WebSignature::TYPE, 'JWT');
		$token->setHeader(WebSignature::ALGORITHM, 'HS256');
		$token->setClaim('Oo');

		$compact = $token->getCompact($key);

		$this->assertEquals('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.T28.fTqVAlaLB0Ri4QS8-PfBjSuTSLgqwdmePBNZ-X2GBm4', $compact);

		$this->assertEquals('JWT', $token->getHeader(WebSignature::TYPE));
		$this->assertEquals('HS256', $token->getHeader(WebSignature::ALGORITHM));
		$this->assertEquals('Oo', $token->getClaim());

		// consume signature
		$token = WebSignature::parse($compact);

		$this->assertEquals('JWT', $token->getHeader(WebSignature::TYPE));
		$this->assertEquals('HS256', $token->getHeader(WebSignature::ALGORITHM));
		$this->assertEquals('Oo', $token->getClaim());
		$this->assertEquals('fTqVAlaLB0Ri4QS8-PfBjSuTSLgqwdmePBNZ-X2GBm4', $token->getSignature());
		$this->assertEquals(true, $token->validate($key));
	}

	public function testParse()
	{
		$jwt   = 'eyJ0eXAiOiJKV1QiLA0KICJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJqb2UiLA0KICJleHAiOjEzMDA4MTkzODAsDQogImh0dHA6Ly9leGFtcGxlLmNvbS9pc19yb290Ijp0cnVlfQ.dBjftJeZ4CVP-mB92K27uhbUJU1p1r_wW1gFWFOEjXk';
		$token = WebSignature::parse($jwt);

		$this->assertInstanceOf('PSX\Json\WebSignature', $token);
		$this->assertEquals('JWT', $token->getHeader(WebSignature::TYPE));
		$this->assertEquals('HS256', $token->getHeader(WebSignature::ALGORITHM));
		$this->assertEquals(array('typ' => 'JWT', 'alg' => 'HS256'), $token->getHeaders());
		$this->assertEquals('{"iss":"joe",' . "\r\n" . ' "exp":1300819380,' . "\r\n" . ' "http://example.com/is_root":true}', $token->getClaim());
	}

	public function testValidate()
	{
		$jwt   = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOjEyMzQ1Njc4OTAsIm5hbWUiOiJKb2huIERvZSIsImFkbWluIjp0cnVlfQ.eoaDVGTClRdfxUZXiPs3f8FmJDkDE_VCQFXqKxpLsts';
		$token = WebSignature::parse($jwt);

		$this->assertInstanceOf('PSX\Json\WebSignature', $token);
		$this->assertEquals('JWT', $token->getHeader(WebSignature::TYPE));
		$this->assertEquals('HS256', $token->getHeader(WebSignature::ALGORITHM));
		$this->assertEquals(array('typ' => 'JWT', 'alg' => 'HS256'), $token->getHeaders());
		$this->assertEquals('{"sub":1234567890,"name":"John Doe","admin":true}', $token->getClaim());
		$this->assertEquals('eoaDVGTClRdfxUZXiPs3f8FmJDkDE_VCQFXqKxpLsts', $token->getSignature());
		$this->assertTrue($token->validate('secret'));
		$this->assertFalse($token->validate('secre'));
	}

	public function testValidateInvalidHeader()
	{
		$jwt   = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOjEyMzQ1Njc4OTAsIm5hbWUiOiJKb2huIERvZSIsImFkbWluIjp0cnVlfQ.eoaDVGTClRdfxUZXiPs3f8FmJDkDE_VCQFXqKxpLsts';
		$token = WebSignature::parse($jwt);
		$token->setHeader('foo', 'bar');

		$this->assertFalse($token->validate('secret'));
	}

	public function testValidateInvalidClaim()
	{
		$jwt   = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOjEyMzQ1Njc4OTAsIm5hbWUiOiJKb2huIERvZSIsImFkbWluIjp0cnVlfQ.eoaDVGTClRdfxUZXiPs3f8FmJDkDE_VCQFXqKxpLsts';
		$token = WebSignature::parse($jwt);
		$token->setClaim('foobar');

		$this->assertFalse($token->validate('secret'));
	}

	public function testValidateInvalidSignature()
	{
		$jwt   = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOjEyMzQ1Njc4OTAsIm5hbWUiOiJKb2huIERvZSIsImFkbWluIjp0cnVlfQ.eoaDVGTClRdfxUZXiPs3f8FmJDkDE_VCQFXqKxpLsts';
		$token = WebSignature::parse($jwt);
		$token->setSignature('foobar');

		$this->assertFalse($token->validate('secret'));
	}

	/**
	 * @expectedException PSX\Exception
	 */
	public function testValidateNoSignature()
	{
		$token = new WebSignature();
		$token->setHeader(WebSignature::TYPE, 'JWT');
		$token->setHeader(WebSignature::ALGORITHM, 'HS256');
		$token->setClaim('Oo');

		$token->validate('foo');
	}

	public function testGetCompact()
	{
		$token = new WebSignature();
		$token->setHeader(WebSignature::TYPE, 'JWT');
		$token->setHeader(WebSignature::ALGORITHM, 'HS256');
		$token->setClaim('Oo');

		$this->assertEquals('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.T28.tI9MeG16DRzjPAR9_BakvyV9xKibXAgB24ZSg3zs4bQ', $token->getCompact('foobar'));
	}

	public function testGetJson()
	{
		$token = new WebSignature();
		$token->setHeader(WebSignature::TYPE, 'JWT');
		$token->setHeader(WebSignature::ALGORITHM, 'HS256');
		$token->setClaim('Oo');

		$this->assertJsonStringEqualsJsonString('{"protected":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9","payload":"T28","signature":"tI9MeG16DRzjPAR9_BakvyV9xKibXAgB24ZSg3zs4bQ"}', $token->getJson('foobar'));
	}

	public function testBase64Encode()
	{
		$data = "\x7b\x22\x74\x79\x70\x22\x3a\x22\x4a\x57\x54\x22\x2c\x0d\x0a";
		$data.= "\x20\x22\x61\x6c\x67\x22\x3a\x22\x48\x53\x32\x35\x36\x22\x7d";

		$this->assertEquals('eyJ0eXAiOiJKV1QiLA0KICJhbGciOiJIUzI1NiJ9', WebSignature::base64Encode($data));

		$data = "\x7b\x22\x69\x73\x73\x22\x3a\x22\x6a\x6f\x65\x22\x2c\x0d\x0a";
		$data.= "\x20\x22\x65\x78\x70\x22\x3a\x31\x33\x30\x30\x38\x31\x39\x33";
		$data.= "\x38\x30\x2c\x0d\x0a\x20\x22\x68\x74\x74\x70\x3a\x2f\x2f\x65";
		$data.= "\x78\x61\x6d\x70\x6c\x65\x2e\x63\x6f\x6d\x2f\x69\x73\x5f\x72";
		$data.= "\x6f\x6f\x74\x22\x3a\x74\x72\x75\x65\x7d";

		$this->assertEquals('eyJpc3MiOiJqb2UiLA0KICJleHAiOjEzMDA4MTkzODAsDQogImh0dHA6Ly9leGFtcGxlLmNvbS9pc19yb290Ijp0cnVlfQ', WebSignature::base64Encode($data));
	}
}
