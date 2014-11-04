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

namespace PSX\Dependency;

use PSX\Handler\HandlerRegistry;
use PSX\Handler\Manager;

/**
 * Handler
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
trait Handler
{
	/**
	 * @return PSX\Sql\TableManager
	 */
	public function getTableManager()
	{
		return new Manager\TableManager($this->get('connection'));
	}

	/**
	 * @return PSX\Handler\HandlerRegistry
	 */
	public function getHandlerRegistry()
	{
		$registry = new HandlerRegistry();
		$registry->add($this->get('table_manager'));
		$registry->add(new Manager\ConnectionManager($this->get('connection')));
		#$registry->add(new Manager\EntityManager($this->get('entity_manager')));
		$registry->add(new Manager\DefaultManager());

		return $registry;
	}
}
