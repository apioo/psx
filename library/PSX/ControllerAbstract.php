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

namespace PSX;

use PSX\ApplicationStackInterface;
use PSX\ControllerInterface;
use PSX\Controller\Behaviour;
use PSX\Data\Record;
use PSX\Dispatch\Filter\ControllerExecutor;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Loader\Context;

/**
 * ControllerAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class ControllerAbstract implements ControllerInterface, ApplicationStackInterface
{
	use Behaviour\AccessorTrait;
	use Behaviour\BodyTrait;
	use Behaviour\HttpTrait;
	use Behaviour\ImporterTrait;
	use Behaviour\RedirectTrait;

	/**
	 * @var PSX\Http\RequestInterface
	 */
	protected $request;

	/**
	 * @var PSX\Http\ResponseInterface
	 */
	protected $response;

	/**
	 * @var PSX\Loader\Context
	 */
	protected $context;

	/**
	 * @var array
	 */
	protected $uriFragments;

	/**
	 * @Inject
	 * @var PSX\Config
	 */
	protected $config;

	/**
	 * @Inject
	 * @var PSX\Validate
	 */
	protected $validate;

	/**
	 * @param PSX\Http\RequestInterface $request
	 * @param PSX\Http\ResponseInterface $response
	 * @param PSX\Loader\Context $context
	 */
	public function __construct(RequestInterface $request, ResponseInterface $response, Context $context = null)
	{
		$this->request      = $request;
		$this->response     = $response;
		$this->context      = $context ?: new Context();
		$this->uriFragments = $this->context->get(Context::KEY_FRAGMENT) ?: array();
	}

	public function getApplicationStack()
	{
		return array_merge(
			$this->getPreFilter(), 
			array(new ControllerExecutor($this, $this->context)), 
			$this->getPostFilter()
		);
	}

	public function getPreFilter()
	{
		return array();
	}

	public function getPostFilter()
	{
		return array();
	}

	public function onLoad()
	{
	}

	public function onDelete()
	{
	}

	public function onGet()
	{
	}

	public function onHead()
	{
	}

	public function onOptions()
	{
	}

	public function onPost()
	{
	}

	public function onPut()
	{
	}

	public function onTrace()
	{
	}

	public function processResponse()
	{
		$body = $this->response->getBody();

		if($body->tell() == 0 && !$this->_responseWritten)
		{
			$this->setBody(new Record());
		}
	}

	/**
	 * Returns an specific uri fragment
	 *
	 * @param string $key
	 * @return string
	 */
	protected function getUriFragment($key)
	{
		return isset($this->uriFragments[$key]) ? $this->uriFragments[$key] : null;
	}
}
