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
class PSX_Payment_Paypal_Store_Session implements PSX_Payment_Paypal_StoreInterface
{
	public function __construct()
	{
		if(!isset($_SESSION[__CLASS__]))
		{
			$_SESSION[__CLASS__] = array();
		}
	}

	public function loadToken($sessId)
	{
		return isset($_SESSION[__CLASS__]['token']) ? $_SESSION[__CLASS__]['token'] : false;
	}

	public function saveToken($sessId, $token)
	{
		$_SESSION[__CLASS__]['token'] = $token;
	}

	public function loadPayerId($sessId)
	{
		return isset($_SESSION[__CLASS__]['payerId']) ? $_SESSION[__CLASS__]['payerId'] : false;
	}

	public function savePayerId($sessId, $payerId)
	{
		$_SESSION[__CLASS__]['payerId'] = $payerId;
	}
}
