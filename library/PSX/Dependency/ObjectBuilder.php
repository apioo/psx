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

use InvalidArgumentException;
use ReflectionClass;
use PSX\Util\Annotation;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ObjectBuilder
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ObjectBuilder implements ObjectBuilderInterface
{
	protected $container;

	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	public function getObject($className, array $constructorArguments = array(), $instanceOf = null)
	{
		$class  = new ReflectionClass($className);
		$object = $class->newInstanceArgs($constructorArguments);

		if($instanceOf !== null && !$object instanceof $instanceOf)
		{
			throw new InvalidArgumentException('Class ' . $className . ' must be an instanceof ' . $instanceOf);
		}

		foreach($class->getProperties() as $property)
		{
			if(strpos($property->getDocComment(), '@Inject') !== false)
			{
				$doc = Annotation::parse($property->getDocComment());

				if($doc->hasAnnotation('Inject'))
				{
					$name = $doc->getFirstAnnotation('Inject');
					if(empty($name))
					{
						$name = $property->getName();
					}

					$property->setAccessible(true);
					$property->setValue($object, $this->container->get($name));
				}
			}
		}

		return $object;
	}
}
