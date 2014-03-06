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
 * Merchant
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Merchant extends Customer
{
	protected $payToEmail;
	protected $recipientDescription;
	protected $transactionId;
	protected $returnUrl;
	protected $returnUrlText;
	protected $returnUrlTarget;
	protected $cancelUrl;
	protected $cancelUrlTarget;
	protected $statusUrl;
	protected $statusUrl2;
	protected $newWindowRedirect;
	protected $language;
	protected $hideLogin;
	protected $confirmationNote;
	protected $logoUrl;
	protected $prepareOnly;
	protected $rid;
	protected $extRefId;
	protected $merchantFields;

	public function getRecordInfo()
	{
		return new RecordInfo('merchant', array(
			'pay_to_email'          => $this->payToEmail,
			'recipient_description' => $this->recipientDescription,
			'transaction_id'        => $this->transactionId,
			'return_url'            => $this->returnUrl,
			'return_url_text'       => $this->returnUrlText,
			'return_url_target'     => $this->returnUrlTarget,
			'cancel_url'            => $this->cancelUrl,
			'cancel_url_target'     => $this->cancelUrlTarget,
			'status_url'            => $this->statusUrl,
			'status_url2'           => $this->statusUrl2,
			'new_window_redirect'   => $this->newWindowRedirect,
			'language'              => $this->language,
			'hide_login'            => $this->hideLogin,
			'confirmation_note'     => $this->confirmationNote,
			'logo_url'              => $this->logoUrl,
			'prepare_only'          => $this->prepareOnly,
			'rid'                   => $this->rid,
			'ext_ref_id'            => $this->extRefId,
			'merchant_fields'       => $this->merchantFields,
		), parent::getRecordInfo());
	}

	public function setPayToEmail($payToEmail)
	{
		$this->payToEmail = $payToEmail;
	}
	
	public function getPayToEmail()
	{
		return $this->payToEmail;
	}

	public function setRecipientDescription($recipientDescription)
	{
		$this->recipientDescription = $recipientDescription;
	}
	
	public function getRecipientDescription()
	{
		return $this->recipientDescription;
	}

	public function setTransactionId($transactionId)
	{
		$this->transactionId = $transactionId;
	}
	
	public function getTransactionId()
	{
		return $this->transactionId;
	}

	public function setReturnUrl($returnUrl)
	{
		$this->returnUrl = $returnUrl;
	}
	
	public function getReturnUrl()
	{
		return $this->returnUrl;
	}

	public function setReturnUrlText($returnUrlText)
	{
		$this->returnUrlText = $returnUrlText;
	}
	
	public function getReturnUrlText()
	{
		return $this->returnUrlText;
	}

	public function setReturnUrlTarget($returnUrlTarget)
	{
		$this->returnUrlTarget = $returnUrlTarget;
	}
	
	public function getReturnUrlTarget()
	{
		return $this->returnUrlTarget;
	}

	public function setCancelUrl($cancelUrl)
	{
		$this->cancelUrl = $cancelUrl;
	}
	
	public function getCancelUrl()
	{
		return $this->cancelUrl;
	}

	public function setCancelUrlTarget($cancelUrlTarget)
	{
		$this->cancelUrlTarget = $cancelUrlTarget;
	}
	
	public function getCancelUrlTarget()
	{
		return $this->cancelUrlTarget;
	}

	public function setStatusUrl($statusUrl)
	{
		$this->statusUrl = $statusUrl;
	}
	
	public function getStatusUrl()
	{
		return $this->statusUrl;
	}

	public function setStatusUrl2($statusUrl2)
	{
		$this->statusUrl2 = $statusUrl2;
	}
	
	public function getStatusUrl2()
	{
		return $this->statusUrl2;
	}

	public function setNewWindowRedirect($newWindowRedirect)
	{
		$this->newWindowRedirect = $newWindowRedirect;
	}
	
	public function getNewWindowRedirect()
	{
		return $this->newWindowRedirect;
	}

	public function setLanguage($language)
	{
		$this->language = $language;
	}
	
	public function getLanguage()
	{
		return $this->language;
	}

	public function setHideLogin($hideLogin)
	{
		$this->hideLogin = $hideLogin;
	}
	
	public function getHideLogin()
	{
		return $this->hideLogin;
	}

	public function setConfirmationNote($confirmationNote)
	{
		$this->confirmationNote = $confirmationNote;
	}
	
	public function getConfirmationNote()
	{
		return $this->confirmationNote;
	}

	public function setLogoUrl($logoUrl)
	{
		$this->logoUrl = $logoUrl;
	}
	
	public function getLogoUrl()
	{
		return $this->logoUrl;
	}

	public function setPrepareOnly($prepareOnly)
	{
		$this->prepareOnly = $prepareOnly;
	}
	
	public function getPrepareOnly()
	{
		return $this->prepareOnly;
	}

	public function setRid($rid)
	{
		$this->rid = $rid;
	}
	
	public function getRid()
	{
		return $this->rid;
	}

	public function setExtRefId($extRefId)
	{
		$this->extRefId = $extRefId;
	}
	
	public function getExtRefId()
	{
		return $this->extRefId;
	}

	public function setMerchantFields($merchantFields)
	{
		$this->merchantFields = $merchantFields;
	}
	
	public function getMerchantFields()
	{
		return $this->merchantFields;
	}
}
