<?php
/*
 *  $Id: ExpressCheckoutTest.php 542 2012-07-10 20:20:59Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Payment\Paypal;

use PSX\Http;
use PSX\Payment\Paypal\Store\Session;

/**
 * PSX_Payment_Paypal_ExpressCheckoutTest
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 542 $
 */
class ExpressCheckoutTest extends \PHPUnit_Framework_TestCase
{
	private $http;
	private $ec;

	protected function setUp()
	{
		$this->http = new Http();
		$this->ec   = new ExpressCheckout($this->http);
	}

	/**
	 * This test is for testing a basic express checkout against the paypal api.
	 * Unfortunatly it is not possible to make the test complete autmoatic
	 * because you have to login and gets redirected to the callback url. So as
	 * default we skip the test
	 */
	public function testExpressCheckout()
	{
		$this->markTestSkipped('This test requires user interaction');

		// set store
		$store = new Session();

		$this->ec->setStore($store);

		// testSetExpressCheckout
		$data = $this->ec->setExpressCheckout(12.00, 'http://127.0.0.1/payment/return', 'http://127.0.0.1/payment/cancel');

		$this->assertEquals(true, isset($data['TOKEN']));
		$this->assertEquals(true, isset($data['TIMESTAMP']));
		$this->assertEquals(true, isset($data['CORRELATIONID']));
		$this->assertEquals(true, isset($data['ACK']));
		$this->assertEquals(PSX_Payment_Paypal_ExpressCheckout::VER, $data['VERSION']);
		$this->assertEquals(true, isset($data['BUILD']));

		// now you have to login and pay the amount with an test account
		echo "\n" . 'URL: ' . ExpressCheckout::WEB . '?cmd=_express-checkout&token=' . urlencode($data['TOKEN']) . "\n";
		echo 'Press Enter to continue' . "\n";

		fgets(STDIN);

		// testGetExpressCheckoutDetails
		$token = $data['TOKEN'];

		$store->saveToken(ExpressCheckout::getSessionId(), $token);

		$data = $this->ec->getExpressCheckoutDetails();

		$this->assertEquals(true, isset($data['TOKEN']));
		$this->assertEquals(true, isset($data['PAYERID']));

		// testDoExpressCheckoutPayment
		$payerId = $data['PAYERID'];

		$store->savePayerId(ExpressCheckout::getSessionId(), $payerId);

		$data = $this->ec->doExpressCheckoutPayment(12.00);

		$this->assertEquals(true, isset($data['TOKEN']));
		$this->assertEquals(true, isset($data['ACK']));
		$this->assertEquals('Success', $data['ACK']);
	}
}
