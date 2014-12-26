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

namespace PSX\Http;

/**
 * CookieTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
