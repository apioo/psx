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

namespace PSX\Payment\Paypal\Data;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Amount
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Amount extends RecordAbstract
{
	public static $currencyCodes = array('AUD', 'BRL', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'NOK', 'NZD', 'PHP', 'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'TWD', 'THB', 'TRY', 'USD');

	protected $currency;
	protected $total;
	protected $details;

	public function getRecordInfo()
	{
		return new RecordInfo('amount', array(
			'currency' => $this->currency,
			'total'    => $this->total,
			'details'  => $this->details,
		));
	}

	public function getCurrency()
	{
		return $this->currency;
	}

	public function setCurrency($currency)
	{
		if(!in_array($currency, self::$currencyCodes))
		{
			throw new Exception('Invalid currency');
		}

		$this->currency = $currency;
	}

	public function getTotal()
	{
		return $this->total;
	}

	public function setTotal($total)
	{
		$total = (float) $total;
		$total = round($total, 2);

		if(strlen($total) > 10)
		{
			throw new Exception('Max 10 characters');
		}

		$this->total = $total;
	}

	public function getDetails()
	{
		return $this->details;
	}

	/**
	 * @param PSX\Payment\Paypal\Data\Details $details
	 */
	public function setDetails(Details $details)
	{
		$this->details = $details;
	}
}
