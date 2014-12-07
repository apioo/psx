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

namespace PSX\Api\Documentation\Generator;

use PSX\Api\Documentation\Data;
use PSX\Api\Documentation\GeneratorInterface;
use PSX\Api\Documentation\Generator\Sample\LoaderInterface;
use PSX\Api\View;
use PSX\Data\Schema\Generator;

/**
 * Sample
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Sample implements GeneratorInterface
{
	protected $loader;

	public function __construct(LoaderInterface $loader)
	{
		$this->loader = $loader;
	}

	public function generate($path, View $view)
	{
		$data    = new Data();
		$methods = array(View::METHOD_GET, View::METHOD_POST, View::METHOD_PUT, View::METHOD_DELETE);
		$types   = array(View::TYPE_REQUEST, View::TYPE_RESPONSE);

		foreach($methods as $method)
		{
			foreach($types as $type)
			{
				$modifier = $method | $type;
				$result   = $this->loader->get($modifier, $path);

				if(!empty($result))
				{
					$data->set($modifier, $result);
				}
			}
		}

		return $data;
	}
}
