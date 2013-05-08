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

namespace PSX\Module;

use PSX\Base;
use PSX\Exception;
use PSX\Dependency\View;
use PSX\ModuleAbstract;

/**
 * ViewAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ViewAbstract extends ModuleAbstract
{
	public function getDependencies()
	{
		return new View($this->base->getConfig());
	}

	public function processResponse($content)
	{
		if(empty($content))
		{
			if(!($response = $this->template->transform()))
			{
				throw new Exception('Error while transforming template');
			}


			$acceptEncoding = Base::getRequestHeader('Accept-Encoding');

			if($this->config['psx_gzip'] === true && strpos($acceptEncoding, 'gzip') !== false)
			{
				header('Content-Encoding: gzip');

				$response = gzencode($response, 9);
			}

			return $response;
		}
		else
		{
			return $content;
		}
	}
}

