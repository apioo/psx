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

namespace PSX\Controller;

use PSX\Base;
use PSX\Data\Record;
use PSX\Data\Writer;
use PSX\Dependency\View;
use PSX\Dispatch\RequestFilter\GzipEncode;
use PSX\Exception;
use PSX\Loader\Location;
use PSX\ControllerAbstract;
use PSX\Http\Request;
use PSX\Http\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ViewAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ViewAbstract extends ControllerAbstract
{
	public function __construct(ContainerInterface $container, Location $location, Request $request, Response $response, array $uriFragments)
	{
		parent::__construct($container, $location, $request, $response, $uriFragments);

		// set controller class to html writer for automatic template file 
		// detection
		$writer = $this->getWriterFactory()->getWriterByContentType('text/html');

		if($writer instanceof Writer\Html)
		{
			$writer->setBaseDir(PSX_PATH_LIBRARY);
			$writer->setControllerClass(get_class($this));
		}
	}

	/**
	 * In case we have not written any response we write an empty response so
	 * that the html write writes the template
	 */
	public function processResponse()
	{
		if($this->response->getBody()->tell() == 0)
		{
			$this->setResponse(new Record());
		}
	}
}
