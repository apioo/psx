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

use PSX\DependencyAbstract;
use PSX\Validate;
use PSX\Input;
use PSX\Sql;
use PSX\Http;

/**
 * Request
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Request extends DependencyAbstract
{
	public function getValidate()
	{
		return new Validate();
	}

	public function getInputGet()
	{
		return new Input\Get($this->get('validate'));
	}

	public function getInputPost()
	{
		return new Input\Get($this->get('validate'));
	}

	public function getSql()
	{
		$config = $this->get('config');

		return new Sql($config['psx_sql_host'],
			$config['psx_sql_user'],
			$config['psx_sql_pw'],
			$config['psx_sql_db']);
	}

	public function getHttp()
	{
		return new Http();
	}
}
