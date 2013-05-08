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

namespace PSX\OpenSocial\Data;

use PSX\OpenSocial\DataAbstract;

/**
 * Account
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Account extends DataAbstract
{
	protected $domain;
	protected $username;
	protected $userId;

	public function getName()
	{
		return 'account';
	}

	public function getFields()
	{
		return array(

			'domain'   => $this->domain,
			'username' => $this->username,
			'userId'   => $this->userId,

		);
	}

	/**
	 * @param string
	 */
	public function setDomain($domain)
	{
		$this->domain = $domain;
	}
	
	public function getDomain()
	{
		return $this->domain;
	}

	/**
	 * @param string
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}
	
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @param string
	 */
	public function setUserId($userId)
	{
		$this->userId = $userId;
	}
	
	public function getUserId()
	{
		return $this->userId;
	}
}

