<?php
/*
 *  $Id: Session.php 542 2012-07-10 20:20:59Z k42b3.x@googlemail.com $
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

namespace PSX\Payment\Paypal\Store;

use PSX\Payment\Paypal\StoreInterface;

/**
 * PSX_Payment_Paypal_StoreInterface
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Payment
 * @version    $Revision: 542 $
 */
class Session implements StoreInterface
{
	public function __construct()
	{
		$key = $this->getName();

		if(!isset($_SESSION[$key]))
		{
			$_SESSION[$key] = array();
		}
	}

	public function loadToken($sessId)
	{
		$key = $this->getName();

		return isset($_SESSION[$key]['token']) ? $_SESSION[$key]['token'] : false;
	}

	public function saveToken($sessId, $token)
	{
		$key = $this->getName();

		$_SESSION[$key]['token'] = $token;
	}

	public function loadPayerId($sessId)
	{
		$key = $this->getName();

		return isset($_SESSION[$key]['payerId']) ? $_SESSION[$key]['payerId'] : false;
	}

	public function savePayerId($sessId, $payerId)
	{
		$key = $this->getName();

		$_SESSION[$key]['payerId'] = $payerId;
	}

	public function getName()
	{
		return __CLASS__;
	}
}
