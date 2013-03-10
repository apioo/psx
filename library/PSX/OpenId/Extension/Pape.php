<?php
/*
 *  $Id: Pape.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

namespace PSX\OpenId\Extension;

use PSX\Exception;
use PSX\OpenId\ExtensionInterface;

/**
 * PSX_OpenId_Extension_Pape
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_OpenId
 * @version    $Revision: 480 $
 */
class Pape implements ExtensionInterface
{
	const NS = 'http://specs.openid.net/extensions/pape/1.0';

	public static $policies = array(

		'phishing-resistant'    => 'http://schemas.openid.net/pape/policies/2007/06/phishing-resistant',
		'multi-factor'          => 'http://schemas.openid.net/pape/policies/2007/06/multi-factor',
		'multi-factor-physical' => 'http://schemas.openid.net/pape/policies/2007/06/multi-factor-physical',

	);

	private $maxAuthAge            = false;
	private $preferredAuthPolicies = array();

	public function __construct(array $preferredAuthPolicies, $maxAuthAge = false)
	{
		foreach($preferredAuthPolicies as $policy)
		{
			$this->addPreferredAuthPolicies($policy);
		}

		if($maxAuthAge !== false)
		{
			$this->setMaxAuthAge($maxAuthAge);
		}
	}

	public function setMaxAuthAge($age)
	{
		$this->maxAuthAge = intval($age);
	}

	public function addPreferredAuthPolicies($policy)
	{
		if(isset(self::$policies[$policy]))
		{
			$this->preferredAuthPolicies[] = self::$policies[$policy];
		}
		else
		{
			throw new Exception('Invalid auth policy ' . $policy);
		}
	}

	public function getParams()
	{
		$params = array();

		$params['openid.ns.pape'] = self::NS;

		if($this->maxAuthAge !== false)
		{
			$params['openid.pape.max_auth_age'] = $this->maxAuthAge;
		}

		if(!empty($this->preferredAuthPolicies))
		{
			$params['openid.pape.preferred_auth_policies'] = implode(' ', $this->preferredAuthPolicies);
		}
		else
		{
			$params['openid.pape.preferred_auth_policies'] = '';
		}

		return $params;
	}

	public function getNs()
	{
		return self::NS;
	}
}
