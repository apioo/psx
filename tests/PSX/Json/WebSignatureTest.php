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

namespace PSX\Json;

use PSX\Data\ReaderResult;
use PSX\Data\ReaderInterface;

/**
 * WebSignatureTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class WebSignatureTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

	public function testBasic()
	{
		// signing key
		$key = 'AyM1SysPpbyDfgZld3umj1qzKObwVMkoqQ-EstJQLr_T-1qS0gZH75aKtMN3Yj0iPS4hcgUuTwjAzZr1Z9CAow';

		// produce signature
		$signature = new WebSignature();
		$signature->setTyp('JWT');
		$signature->setAlg('HS256');
		$signature->setPayLoad('Oo');

		$token = $signature->getCompact($key);

		$this->assertEquals('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.T28.fTqVAlaLB0Ri4QS8-PfBjSuTSLgqwdmePBNZ-X2GBm4', $token);

		$this->assertEquals('HS256', $signature->getAlg());
		$this->assertEquals('JWT', $signature->getTyp());
		$this->assertEquals('Oo', $signature->getPayload());
		$this->assertEquals('fTqVAlaLB0Ri4QS8-PfBjSuTSLgqwdmePBNZ-X2GBm4', $signature->getSignature());
		$this->assertEquals(true, $signature->validate($key));

		// consume signature
		$signature = new WebSignature($token);

		$this->assertEquals('HS256', $signature->getAlg());
		$this->assertEquals('JWT', $signature->getTyp());
		$this->assertEquals('Oo', $signature->getPayload());
		$this->assertEquals('fTqVAlaLB0Ri4QS8-PfBjSuTSLgqwdmePBNZ-X2GBm4', $signature->getSignature());
		$this->assertEquals(true, $signature->validate($key));
	}

	public function testRfc()
	{
		// @todo add test cases from the RFC
	}
}
