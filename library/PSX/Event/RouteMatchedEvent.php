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

namespace PSX\Event;

use PSX\Loader\Context;
use PSX\Http\RequestInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * RouteMatchedEvent
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     http://www.ietf.org/rfc/rfc4287.txt
 */
class RouteMatchedEvent extends Event
{
	protected $request;
	protected $context;

	public function __construct(RequestInterface $request, Context $context)
	{
		$this->request = $request;
		$this->context = $context;
	}

	public function getRequest()
	{
		return $this->request;
	}

	public function getContext()
	{
		return $this->context;
	}
}
