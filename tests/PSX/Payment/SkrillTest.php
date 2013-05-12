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

namespace PSX\Payment;

use PSX\Http;
use PSX\Payment\Skrill;
use PSX\Payment\Skrill\Data;
use PSX\Url;

/**
 * SkrillTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class SkrillTest extends \PHPUnit_Framework_TestCase
{
	private $http;
	private $skrill;

	protected function setUp()
	{
		$this->http   = new Http();
		$this->skrill = new Skrill($this->http);
	}

	public function testCreatePayment()
	{
		// create simple payment
		$payment = new Data\Payment();
		$payment->setPayToEmail('merchant@skrill.com');
		$payment->setStatusUrl('merchant@skrill.com');
		$payment->setLanguage('EN');
		$payment->setAmount(39.60);
		$payment->setCurrency('GBP');
		$payment->setDetail1Description('Description:');
		$payment->setDetail1Text('Romeo and Juliet (W. Shakespeare)');
		$payment->setConfirmationNote('Samplemerchant wishes you pleasure reading your new book!');

		$return = $this->skrill->createPayment($payment);

		$this->assertEquals(true, $return);
	}
}
