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

namespace PSX\Loader\CallbackResolver;

use RuntimeException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use PSX\Dispatch\ControllerFactoryInterface;
use PSX\Exception;
use PSX\Loader\Callback;
use PSX\Loader\CallbackResolverInterface;
use PSX\Loader\Location;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * DependencyInjector
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DependencyInjector implements CallbackResolverInterface
{
	protected $factory;

	public function __construct(ControllerFactoryInterface $factory)
	{
		$this->factory = $factory;
	}

	public function resolve(Location $location, RequestInterface $request, ResponseInterface $response)
	{
		$source = $location->getParameter(Location::KEY_SOURCE);

		if(strpos($source, '::') !== false)
		{
			list($className, $method) = explode('::', $source, 2);
		}
		else
		{
			$className = $source;
			$method    = null;
		}

		$controller = $this->factory->getController($className, $location, $request, $response);

		return new Callback($controller, $method, array($request, $response));
	}
}
