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

namespace PSX\Http;

/**
 * CookieTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CookieTest extends \PHPUnit_Framework_TestCase
{
	public function testParseSetCookie()
	{
		$raw = 'DNR=deleted; expires=Tue, 24-Dec-2013 11:39:14 GMT; path=/; domain=.www.yahoo.com';

		$cookie = CookieParser::parseSetCookie($raw);

		$this->assertEquals('DNR', $cookie->getName());
		$this->assertEquals('deleted', $cookie->getValue());
		$this->assertEquals(date('r', strtotime('Tue, 24-Dec-2013 11:39:14 GMT')), $cookie->getExpires()->format('r'));
		$this->assertEquals('/', $cookie->getPath());
		$this->assertEquals('www.yahoo.com', $cookie->getDomain());
	}

	public function testParseSetCookieInvalid()
	{
		$cookie = CookieParser::parseSetCookie('');

		$this->assertEquals(null, $cookie);
	}

	public function testParseCookie()
	{
		$raw = 'PREF=ID=b2213461059ce5aa:U=7e750f10b5443cbd:FF=0:LD=de:TM=1355057682:LM=1416137366:IG=1:S=iUo8jIwdvU1XETOk; OGPC=4061130-15:; NID=67=KH_6L9jakszp8JPerRzPmmDlE-6SWo8OBPNDRWybmglbBn1pRkxcZhLgT65ELl0j0nSCP4wodw0UKGsk2SAUp1bGhep2DYqn7dbMYumzmMax2GHl1Y-HzLK4-Ct-9W-yrAH_gRs6DoH25Hn7epA; FOO; BAR=';

		$cookies = CookieParser::parseCookie($raw);

		$this->assertEquals(5, count($cookies));
		$this->assertEquals('PREF', $cookies[0]->getName());
		$this->assertEquals('ID=b2213461059ce5aa:U=7e750f10b5443cbd:FF=0:LD=de:TM=1355057682:LM=1416137366:IG=1:S=iUo8jIwdvU1XETOk', $cookies[0]->getValue());
		$this->assertEquals('OGPC', $cookies[1]->getName());
		$this->assertEquals('4061130-15:', $cookies[1]->getValue());
		$this->assertEquals('NID', $cookies[2]->getName());
		$this->assertEquals('67=KH_6L9jakszp8JPerRzPmmDlE-6SWo8OBPNDRWybmglbBn1pRkxcZhLgT65ELl0j0nSCP4wodw0UKGsk2SAUp1bGhep2DYqn7dbMYumzmMax2GHl1Y-HzLK4-Ct-9W-yrAH_gRs6DoH25Hn7epA', $cookies[2]->getValue());
		$this->assertEquals('FOO', $cookies[3]->getName());
		$this->assertEquals(null, $cookies[3]->getValue());
		$this->assertEquals('BAR', $cookies[4]->getName());
		$this->assertEquals('', $cookies[4]->getValue());
	}
}
