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

namespace PSX\Payment\Paypal;

use PSX\Data\RecordInterface;

/**
 * TestIpnController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TestIpnController extends IpnAbstract
{
	protected function onVerified(RecordInterface $record)
	{
		$this->getTestCase()->assertEquals('Completed', $record->getPaymentStatus());
		$this->getTestCase()->assertEquals(19.95, $record->getMcGross());
		$this->getTestCase()->assertEquals('USD', $record->getMcCurrency());
		$this->getTestCase()->assertEquals('61E67681CH3238416', $record->getTxnId());
		$this->getTestCase()->assertEquals('gpmac_1231902686_biz@paypal.com', $record->getReceiverEmail());
		$this->getTestCase()->assertEquals('gpmac_1231902590_per@paypal.com', $record->getPayerEmail());
	}

	protected function onInvalid(RecordInterface $record)
	{

	}
}
