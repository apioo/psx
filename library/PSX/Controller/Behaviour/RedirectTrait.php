<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Controller\Behaviour;

use PSX\Http\Exception as StatusCode;
use PSX\Url;

/**
 * Provides methods to forward an request to another controller or redirect the
 * client by sending an Location header
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
