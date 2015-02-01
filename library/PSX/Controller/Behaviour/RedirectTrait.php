<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Controller\Behaviour;

use PSX\Http\Exception as StatusCode;
use PSX\Url;

/**
 * Provides methods to forward an request to another controller or redirect the
 * client by sending an Location header
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link	http://phpsx.org
 */
trait RedirectTrait
{
	/**
	 * @Inject
	 * @var PSX\Loader
	 */
	protected $loader;

	/**
	 * @Inject
	 * @var PSX\Loader\ReverseRouter
	 */
	protected $reverseRouter;

	/**
	 * Forwards the request to another controller
	 *
	 * @param string $source
	 * @param array $parameters
	 */
	protected function forward($source, array $parameters = array())
	{
		$path = $this->reverseRouter->getPath($source, $parameters);

		$this->request->setMethod('GET');
		$this->request->getUri()->setPath($path);

		$this->loader->load($this->request, $this->response);
	}

	/**
	 * Throws an redirect exception which sends an Location header. If source is 
	 * not an url the reverse router is used to determine the url
	 *
	 * @param string $source
	 * @param array $parameters
	 * @param integer $code
	 */
	protected function redirect($source, array $parameters = array(), $code = 307)
	{
		if($source instanceof Url)
		{
			$url = $source->toString();
		}
		else if(filter_var($source, FILTER_VALIDATE_URL))
		{
			$url = $source;
		}
		else
		{
			$url = $this->reverseRouter->getUrl($source, $parameters);
		}

		throw new StatusCode\TemporaryRedirectException($url);
	}
}
