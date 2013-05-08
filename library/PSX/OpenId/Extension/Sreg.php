<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

use PSX\OpenId\ExtensionInterface;

/**
 * Sreg
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Sreg implements ExtensionInterface
{
	const NS = 'http://openid.net/extensions/sreg/1.1';

	public static $fields = array('nickname', 'email', 'fullname', 'dob', 'gender', 'postcode', 'country', 'language', 'timezone');

	private $required  = array();
	private $optional  = array();
	private $policyUrl = '';

	public function __construct(array $requiredFields = array(), array $optionalFields = array(), $policyUrl = false)
	{
		foreach($requiredFields as $field)
		{
			$this->addRequiredField($field);
		}

		foreach($optionalFields as $field)
		{
			$this->addOptionalField($field);
		}

		if($policyUrl !== false)
		{
			$this->setPolicyUrl($policyUrl);
		}
	}

	public function addRequiredField($field)
	{
		if(in_array($field, self::$fields))
		{
			$this->required[] = $field;
		}
	}

	public function addOptionalField($field)
	{
		if(in_array($field, self::$fields))
		{
			$this->optional[] = $field;
		}
	}

	public function setPolicyUrl($url)
	{
		$this->policyUrl = $url;
	}

	public function getParams()
	{
		$params = array();

		$params['openid.ns.sreg'] = self::NS;

		if(!empty($this->required))
		{
			$params['openid.sreg.required'] = implode(',', $this->required);
		}

		if(!empty($this->optional))
		{
			$params['openid.sreg.optional'] = implode(',', $this->optional);
		}

		if(!empty($this->policyUrl))
		{
			$params['openid.sreg.policy_url'] = $this->policyUrl;
		}

		return $params;
	}

	public function getNs()
	{
		return self::NS;
	}

	public static function validateFields(array $fields)
	{
		$result = array();

		foreach($fields as $f)
		{
			if(in_array($f, self::$fields))
			{
				$result[] = $f;
			}
		}

		return $result;
	}
}
