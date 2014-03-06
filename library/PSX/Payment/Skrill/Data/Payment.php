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

namespace PSX\Payment\Skrill\Data;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Payment
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Payment extends Merchant
{
	public static $currencyCodes = array('AUD', 'BRL', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'NOK', 'NZD', 'PHP', 'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'TWD', 'THB', 'TRY', 'USD');

	protected $amount;
	protected $currency;
	protected $amount2Description;
	protected $amount2;
	protected $amount3Description;
	protected $amount3;
	protected $amount4Description;
	protected $amount4;
	protected $detail1Description;
	protected $detail1Text;
	protected $detail2Description;
	protected $detail2Text;
	protected $detail3Description;
	protected $detail3Text;
	protected $detail4Description;
	protected $detail4Text;
	protected $detail5Description;
	protected $detail5Text;

	public function getRecordInfo()
	{
		return new RecordInfo('payment', array(
			'amount'              => $this->amount,
			'currency'            => $this->currency,
			'amount2_description' => $this->amount2Description,
			'amount2'             => $this->amount2,
			'amount3_description' => $this->amount3Description,
			'amount3'             => $this->amount3,
			'amount4_description' => $this->amount4Description,
			'amount4'             => $this->amount4,
			'detail1_description' => $this->detail1Description,
			'detail1_text'        => $this->detail1Text,
			'detail2_description' => $this->detail2Description,
			'detail2_text'        => $this->detail2Text,
			'detail3_description' => $this->detail3Description,
			'detail3_text'        => $this->detail3Text,
			'detail4_description' => $this->detail4Description,
			'detail4_text'        => $this->detail4Text,
			'detail5_description' => $this->detail5Description,
			'detail5_text'        => $this->detail5Text,
		), parent::getRecordInfo());
	}

	public function setAmount($amount)
	{
		$amount = (float) $amount;
		$amount = round($amount, 2);

		$this->amount = $amount;
	}
	
	public function getAmount()
	{
		return $this->amount;
	}

	public function setCurrency($currency)
	{
		if(!in_array($currency, self::$currencyCodes))
		{
			throw new Exception('Invalid currency');
		}

		$this->currency = $currency;
	}
	
	public function getCurrency()
	{
		return $this->currency;
	}

	public function setAmount2Description($amount2Description)
	{
		$this->amount2Description = $amount2Description;
	}
	
	public function getAmount2Description()
	{
		return $this->amount2Description;
	}

	public function setAmount2($amount2)
	{
		$this->amount2 = $amount2;
	}
	
	public function getAmount2()
	{
		return $this->amount2;
	}

	public function setAmount3Description($amount3Description)
	{
		$this->amount3Description = $amount3Description;
	}
	
	public function getAmount3Description()
	{
		return $this->amount3Description;
	}

	public function setAmount3($amount3)
	{
		$this->amount3 = $amount3;
	}
	
	public function getAmount3()
	{
		return $this->amount3;
	}

	public function setAmount4Description($amount4Description)
	{
		$this->amount4Description = $amount4Description;
	}
	
	public function getAmount4Description()
	{
		return $this->amount4Description;
	}

	public function setAmount4($amount4)
	{
		$this->amount4 = $amount4;
	}
	
	public function getAmount4()
	{
		return $this->amount4;
	}

	public function setDetail1Description($detail1Description)
	{
		$this->detail1Description = $detail1Description;
	}
	
	public function getDetail1Description()
	{
		return $this->detail1Description;
	}

	public function setDetail1Text($detail1Text)
	{
		$this->detail1Text = $detail1Text;
	}
	
	public function getDetail1Text()
	{
		return $this->detail1Text;
	}

	public function setDetail2Description($detail2Description)
	{
		$this->detail2Description = $detail2Description;
	}
	
	public function getDetail2Description()
	{
		return $this->detail2Description;
	}

	public function setDetail2Text($detail2Text)
	{
		$this->detail2Text = $detail2Text;
	}
	
	public function getDetail2Text()
	{
		return $this->detail2Text;
	}

	public function setDetail3Description($detail3Description)
	{
		$this->detail3Description = $detail3Description;
	}
	
	public function getDetail3Description()
	{
		return $this->detail3Description;
	}

	public function setDetail3Text($detail3Text)
	{
		$this->detail3Text = $detail3Text;
	}
	
	public function getDetail3Text()
	{
		return $this->detail3Text;
	}

	public function setDetail4Description($detail4Description)
	{
		$this->detail4Description = $detail4Description;
	}
	
	public function getDetail4Description()
	{
		return $this->detail4Description;
	}

	public function setDetail4Text($detail4Text)
	{
		$this->detail4Text = $detail4Text;
	}
	
	public function getDetail4Text()
	{
		return $this->detail4Text;
	}

	public function setDetail5Description($detail5Description)
	{
		$this->detail5Description = $detail5Description;
	}
	
	public function getDetail5Description()
	{
		return $this->detail5Description;
	}

	public function setDetail5Text($detail5Text)
	{
		$this->detail5Text = $detail5Text;
	}
	
	public function getDetail5Text()
	{
		return $this->detail5Text;
	}
}
