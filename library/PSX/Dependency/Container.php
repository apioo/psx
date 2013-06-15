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

namespace PSX\Dependency;

use PSX\Base;
use PSX\Config;
use PSX\DependencyAbstract;
use PSX\Dispatch;
use PSX\Http;
use PSX\Input;
use PSX\Loader;
use PSX\Session;
use PSX\Sql;
use PSX\Template;
use PSX\Validate;

/**
 * Container
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Container extends DependencyAbstract
{
	public function getBase()
	{
		return new Base($this->get('config'));
	}

	public function getConfig()
	{
		return new Config($this->getParameter('config.file'));
	}

	public function getDispatch()
	{
		return new Dispatch($this->get('config'), $this->get('loader'));
	}

	public function getHttp()
	{
		return new Http();
	}

	public function getInputCookie()
	{
		return new Input\Cookie($this->get('validate'));
	}

	public function getInputFiles()
	{
		return new Input\Files($this->get('validate'));
	}

	public function getInputGet()
	{
		return new Input\Get($this->get('validate'));
	}

	public function getInputPost()
	{
		return new Input\Get($this->get('validate'));
	}

	public function getInputRequest()
	{
		return new Input\Request($this->get('validate'));
	}

	public function getLoader()
	{
		$loader = new Loader($this);

		// configure loader
		//$loader->addRoute('.well-known/host-meta', 'foo');

		return $loader;
	}

	public function getSession()
	{
		$session = new Session($this->getParameter('session.name'));
		$session->start();

		return $session;
	}

	public function getSql()
	{
		return new Sql($this->get('config')->get('psx_sql_host'),
			$this->get('config')->get('psx_sql_user'),
			$this->get('config')->get('psx_sql_pw'),
			$this->get('config')->get('psx_sql_db'));
	}

	public function getTemplate()
	{
		return new Template();
	}

	public function getValidate()
	{
		return new Validate();
	}
}

