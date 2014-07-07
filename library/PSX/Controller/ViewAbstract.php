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

use Psr\HttpMessage\RequestInterface;
use Psr\HttpMessage\ResponseInterface;
use PSX\ControllerAbstract;
use PSX\Data\Writer;
use PSX\Loader\Location;

/**
 * ViewAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ViewAbstract extends ControllerAbstract
{
	/**
	 * @Inject
	 * @var PSX\TemplateInterface
	 */
	protected $template;

	public function onLoad()
	{
		parent::onLoad();

		// set controller class to html writer for automatic template file 
		// detection
		$writer = $this->writerFactory->getWriterByContentType('text/html');

		if($writer instanceof Writer\Html)
		{
			$writer->setBaseDir(PSX_PATH_LIBRARY);
			$writer->setControllerClass(get_class($this));
		}
	}
}
