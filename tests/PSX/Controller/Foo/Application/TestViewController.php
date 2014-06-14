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

namespace PSX\Controller\Foo\Application;

use PSX\Controller\ViewAbstract;
use PSX\Loader\Location;
use PSX\Http\Request;
use PSX\Http\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * TestViewController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TestViewController extends ViewAbstract
{
	public function __construct(ContainerInterface $container, Location $location, Request $request, Response $response)
	{
		parent::__construct($container, $location, $request, $response);

		$this->getWriterFactory()->getWriterByContentType('text/html')->setBaseDir('tests');
	}

	public function doIndex()
	{
		$this->getTemplate()->assign('foo', 'bar');
	}

	public function doDetail()
	{
		$this->getTemplate()->assign('foo', 'bar');
		$this->getTemplate()->set('detail.tpl');
	}

	public function doExplicit()
	{
		$this->getTemplate()->assign('foo', 'bar');
		$this->getTemplate()->set('tests/PSX/Controller/Foo/Resource/explicit.tpl');
	}
}
