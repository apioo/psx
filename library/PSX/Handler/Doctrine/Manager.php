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

namespace PSX\Handler\Doctrine;

use Closure;
use Doctrine\ORM\EntityManager;
use PSX\Handler\HandlerManagerInterface;

/**
 * Manager
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Manager implements HandlerManagerInterface
{
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	protected $entityManager;

	protected $_container;

	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	public function getHandler($className)
	{
		if($className instanceof Closure)
		{
			return new CallbackHandler($this->entityManager, $className);
		}
		else
		{
			if(!isset($this->_container[$className]))
			{
				$this->_container[$className] = new $className($this->entityManager);
			}

			return $this->_container[$className];
		}
	}
}
