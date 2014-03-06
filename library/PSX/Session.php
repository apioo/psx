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

namespace PSX;

use SessionHandlerInterface;

/**
 * Session
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Session
{
	protected $name;
	protected $token;

	protected $sessionTokenKey;

	public function __construct($name, SessionHandlerInterface $handler = null)
	{
		$this->setSessionTokenKey(__CLASS__);
		$this->setName($name);

		$this->generateToken();

		if($handler !== null)
		{
			$this->setSaveHandler($handler);
		}
	}

	public function set($key, $value)
	{
		$_SESSION[$key] = $value;
	}

	public function get($key)
	{
		return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
	}

	public function has($key)
	{
		return isset($_SESSION[$key]);
	}

	public function __set($key, $value)
	{
		$this->set($key, $value);
	}

	public function __get($key)
	{
		return $this->get($key);
	}

	public function setSessionTokenKey($tokenKey)
	{
		$this->sessionTokenKey = $tokenKey;
	}

	public function getSessionTokenKey()
	{
		return $this->sessionTokenKey;
	}

	public function setName($name)
	{
		session_name($this->name = $name);
	}

	public function getName()
	{
		return $this->name;
	}

	public function setToken($token)
	{
		$this->token = $token;
	}

	public function getToken()
	{
		return $this->token;
	}

	public function setId($id)
	{
		session_id($id);
	}

	public function getId()
	{
		return session_id();
	}

	public function setSaveHandler(SessionHandlerInterface $handler)
	{
		session_set_save_handler($handler);
	}

	public function setSavePath($path)
	{
		session_save_path($path);
	}

	public function start()
	{
		session_start();

		if(isset($_SESSION[$this->sessionTokenKey]) && $_SESSION[$this->sessionTokenKey] === $this->token)
		{
			// we have an valid token
		}
		else
		{
			$_SESSION = array();
			$_SESSION[$this->sessionTokenKey] = $this->token;
		}
	}

	public function close()
	{
		session_write_close();
	}

	public function destroy()
	{
		$_SESSION = array();

		if(isset($_COOKIE[session_name()]))
		{
			setcookie(session_name(), '', time() - 3600);
		}

		if($this->isActive())
		{
			session_destroy();
		}
	}

	public function isActive()
	{
		$sessId = $this->getId();

		return !empty($sessId);
	}

	protected function generateToken()
	{
		$ip = isset($_SERVER['REMOTE_ADDR'])     ? $_SERVER['REMOTE_ADDR']     : '0.0.0.0';
		$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'undefined';

		$this->setToken(md5($this->name . $ip . $ua));
	}
}

