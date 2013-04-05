<?php
/*
 *  $Id: Message.php 488 2012-05-28 12:44:38Z k42b3.x@googlemail.com $
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

namespace PSX\Payment\Paypal\Data;

use PSX\Data\RecordAbstract;

/**
 * Data object wich represents an IPN message send from paypal to the API
 * endpoint
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Payment
 * @version    $Revision: 488 $
 */
class FundingInstrument extends RecordAbstract
{
	protected $creditCard;
	protected $creditCardToken;

	public function getName()
	{
		return 'fundingInstrument';
	}

	public function getFields()
	{
		return array(
			'credit_card'       => $this->creditCard,
			'credit_card_token' => $this->creditCardToken,
		);
	}

	public function setCreditCard(CreditCard $creditCard)
	{
		$this->creditCard = $creditCard;
	}

	public function setCreditCardToken(CreditCardToken $creditCardToken)
	{
		$this->creditCardToken = $creditCardToken;
	}
}
