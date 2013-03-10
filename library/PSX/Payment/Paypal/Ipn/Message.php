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

namespace PSX\Payment\Paypal\Ipn;

use PSX\DateTime;
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
class Message extends RecordAbstract
{
	public $receiverEmail;
	public $receiverId;
	public $residenceCountry;
	public $testIpn;
	public $transactionSubject;
	public $txnId;
	public $txnType;
	public $payerEmail;
	public $payerId;
	public $payerStatus;
	public $firstName;
	public $lastName;
	public $addressCity;
	public $addressCountry;
	public $addressCountryCode;
	public $addressName;
	public $addressState;
	public $addressStatus;
	public $addressStreet;
	public $addressZip;
	public $custom;
	public $handlingAmount;
	public $itemName;
	public $itemNumber;
	public $mcCurrency;
	public $mcFee;
	public $mcGross;
	public $paymentDate;
	public $paymentFee;
	public $paymentGross;
	public $paymentStatus;
	public $paymentType;
	public $protectionEligibility;
	public $quantity;
	public $shipping;
	public $tax;
	public $notifyVersion;
	public $charset;
	public $verifySign;

	public function getName()
	{
		return 'message';
	}

	public function getFields()
	{
		return array(

			'receiver_email'         => $this->receiverEmail,
			'receiver_id'            => $this->receiverId,
			'residence_country'      => $this->residenceCountry,
			'test_ipn'               => $this->testIpn ,
			'transaction_subject'    => $this->transactionSubject ,
			'txn_id'                 => $this->txnId,
			'txn_type'               => $this->txnType,
			'payer_email'            => $this->payerEmail,
			'payer_id'               => $this->payerId,
			'payer_status'           => $this->payerStatus,
			'first_name'             => $this->firstName,
			'last_name'              => $this->lastName,
			'address_city'           => $this->addressCity,
			'address_country'        => $this->addressCountry,
			'address_country_code'   => $this->addressCountryCode,
			'address_name'           => $this->addressName,
			'address_state'          => $this->addressState,
			'address_status'         => $this->addressStatus,
			'address_street'         => $this->addressStreet,
			'address_zip'            => $this->addressZip,
			'custom'                 => $this->custom,
			'handling_amount'        => $this->handlingAmount,
			'item_name'              => $this->itemName,
			'item_number'            => $this->itemNumber,
			'mc_currency'            => $this->mcCurrency,
			'mc_fee'                 => $this->mcFee,
			'mc_gross'               => $this->mcGross,
			'payment_date'           => $this->paymentDate,
			'payment_fee'            => $this->paymentFee,
			'payment_gross'          => $this->paymentGross,
			'payment_status'         => $this->paymentStatus,
			'payment_type'           => $this->paymentType,
			'protection_eligibility' => $this->protectionEligibility,
			'quantity'               => $this->quantity,
			'shipping'               => $this->shipping,
			'tax'                    => $this->tax,
			'notify_version'         => $this->notifyVersion,
			'charset'                => $this->charset,
			'verify_sign'            => $this->verifySign,

		);
	}

	public function setReceiverEmail($receiverEmail)
	{
		$this->receiverEmail = $receiverEmail;
	}

	public function setReceiverId($receiverId)
	{
		$this->receiverId = $receiverId;
	}

	public function setResidenceCountry($residenceCountry)
	{
		$this->residenceCountry = $residenceCountry;
	}

	public function setTestIpn($testIpn)
	{
		$this->testIpn = $testIpn;
	}

	public function setTransactionSubject($transactionSubject)
	{
		$this->transactionSubject = $transactionSubject;
	}

	public function setTxnId($txnId)
	{
		$this->txnId = $txnId;
	}

	public function setTxnType($txnType)
	{
		$this->txnType = $txnType;
	}

	public function setPayerEmail($payerEmail)
	{
		$this->payerEmail = $payerEmail;
	}

	public function setPayerId($payerId)
	{
		$this->payerId = $payerId;
	}

	public function setPayerStatus($payerStatus)
	{
		$this->payerStatus = $payerStatus;
	}

	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;
	}

	public function setLastName($lastName)
	{
		$this->lastName = $lastName;
	}

	public function setAddressCity($addressCity)
	{
		$this->addressCity = $addressCity;
	}

	public function setAddressCountry($addressCountry)
	{
		$this->addressCountry = $addressCountry;
	}

	public function setAddressCountryCode($addressCountryCode)
	{
		$this->addressCountryCode = $addressCountryCode;
	}

	public function setAddressName($addressName)
	{
		$this->addressName = $addressName;
	}

	public function setAddressState($addressState)
	{
		$this->addressState = $addressState;
	}

	public function setAddressStatus($addressStatus)
	{
		$this->addressStatus = $addressStatus;
	}

	public function setAddressStreet($addressStreet)
	{
		$this->addressStreet = $addressStreet;
	}

	public function setAddressZip($addressZip)
	{
		$this->addressZip = $addressZip;
	}

	public function setCustom($custom)
	{
		$this->custom = $custom;
	}

	public function setHandlingAmount($handlingAmount)
	{
		$this->handlingAmount = (float) $handlingAmount;
	}

	public function setItemName($itemName)
	{
		$this->itemName = $itemName;
	}

	public function setItemNumber($itemNumber)
	{
		$this->itemNumber = $itemNumber;
	}

	public function setMcCurrency($mcCurrency)
	{
		$this->mcCurrency = $mcCurrency;
	}

	public function setMcFee($mcFee)
	{
		$this->mcFee = (float) $mcFee;
	}

	public function setMcGross($mcGross)
	{
		$this->mcGross = (float) $mcGross;
	}

	public function setPaymentDate($paymentDate)
	{
		$this->paymentDate = new DateTime($paymentDate);
	}

	public function setPaymentFee($paymentFee)
	{
		$this->paymentFee = (float) $paymentFee;
	}

	public function setPaymentGross($paymentGross)
	{
		$this->paymentGross = (float) $paymentGross;
	}

	public function setPaymentStatus($paymentStatus)
	{
		$this->paymentStatus = $paymentStatus;
	}

	public function setPaymentType($paymentType)
	{
		$this->paymentType = $paymentType;
	}

	public function setProtectionEligibility($protectionEligibility)
	{
		$this->protectionEligibility = $protectionEligibility;
	}

	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;
	}

	public function setShipping($shipping)
	{
		$this->shipping = (float) $shipping;
	}

	public function setTax($tax)
	{
		$this->tax = (float) $tax;
	}

	public function setNotifyVersion($notifyVersion)
	{
		$this->notifyVersion = $notifyVersion;
	}

	public function setCharset($charset)
	{
		$this->charset = $charset;
	}

	public function setVerifySign($verifySign)
	{
		$this->verifySign = $verifySign;
	}
}
