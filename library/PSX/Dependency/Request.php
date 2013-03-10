<?php
/*
 *  $Id: Default.php 533 2012-07-09 20:33:55Z k42b3.x@googlemail.com $
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

namespace PSX\Dependency;

use PSX\DependencyAbstract;
use PSX\Validate;
use PSX\Input\Get;
use PSX\Input\Post;
use PSX\Sql;

/**
 * PSX_Dependency_Default
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Dependency
 * @version    $Revision: 533 $
 */
class Request extends DependencyAbstract
{
	public function setup()
	{
		parent::setup();

		$this->getInputGet();
		$this->getInputPost();
		//$this->getSql();
	}

	public function getValidate()
	{
		if($this->has('validate'))
		{
			return $this->get('validate');
		}

		return $this->set('validate', new Validate());
	}

	public function getInputGet()
	{
		if($this->has('parameter'))
		{
			return $this->get('parameter');
		}

		return $this->set('parameter', new Get($this->getValidate()));
	}

	public function getInputPost()
	{
		if($this->has('body'))
		{
			return $this->get('body');
		}

		return $this->set('body', new Post($this->getValidate()));
	}

	public function getSql()
	{
		if($this->has('sql'))
		{
			return $this->get('sql');
		}

		return $this->set('sql', new Sql(
			$this->config['psx_sql_host'], 
			$this->config['psx_sql_user'], 
			$this->config['psx_sql_pw'], 
			$this->config['psx_sql_db'])
		);
	}
}
