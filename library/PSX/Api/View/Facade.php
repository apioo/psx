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

namespace PSX\Api\View;

use PSX\Api\View;

/**
 * Class to provide a better interface to access the view values
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Facade
{
	protected $view;
	protected $allowedMethods = array();

	public function __construct(View $view)
	{
		$this->view = $view;

		$this->generateAllowedMethods();
	}

	public function getAllowedMethods()
	{
		return $this->allowedMethods;
	}

	public function hasGet()
	{
		return $this->view->has(View::METHOD_GET);
	}

	public function hasGetResponse()
	{
		return $this->view->has(View::METHOD_GET | View::TYPE_RESPONSE);
	}

	public function getGetResponse()
	{
		return $this->view->get(View::METHOD_GET | View::TYPE_RESPONSE);
	}

	public function hasPost()
	{
		return $this->view->has(View::METHOD_POST);
	}

	public function hasPostRequest()
	{
		return $this->view->has(View::METHOD_POST | View::TYPE_REQUEST);
	}

	public function getPostRequest()
	{
		return $this->view->get(View::METHOD_POST | View::TYPE_REQUEST);
	}

	public function hasPostResponse()
	{
		return $this->view->has(View::METHOD_POST | View::TYPE_RESPONSE);
	}

	public function getPostResponse()
	{
		return $this->view->get(View::METHOD_POST | View::TYPE_RESPONSE);
	}

	public function hasPut()
	{
		return $this->view->has(View::METHOD_PUT);
	}

	public function hasPutRequest()
	{
		return $this->view->has(View::METHOD_PUT | View::TYPE_REQUEST);
	}

	public function getPutRequest()
	{
		return $this->view->get(View::METHOD_PUT | View::TYPE_REQUEST);
	}

	public function hasPutResponse()
	{
		return $this->view->has(View::METHOD_PUT | View::TYPE_RESPONSE);
	}

	public function getPutResponse()
	{
		return $this->view->get(View::METHOD_PUT | View::TYPE_RESPONSE);
	}

	public function hasDelete()
	{
		return $this->view->has(View::METHOD_DELETE);
	}

	public function hasDeleteRequest()
	{
		return $this->view->has(View::METHOD_DELETE | View::TYPE_REQUEST);
	}

	public function getDeleteRequest()
	{
		return $this->view->get(View::METHOD_DELETE | View::TYPE_REQUEST);
	}

	public function hasDeleteResponse()
	{
		return $this->view->has(View::METHOD_DELETE | View::TYPE_RESPONSE);
	}

	public function getDeleteResponse()
	{
		return $this->view->get(View::METHOD_DELETE | View::TYPE_RESPONSE);
	}

	protected function generateAllowedMethods()
	{
		$options = 0;
		foreach($this->view as $key => $view)
		{
			$options|= $key;
		}

		$result  = array();
		$methods = View::getMethods();

		foreach($methods as $method => $methodName)
		{
			if($options & $method)
			{
				$this->allowedMethods[] = $methodName;
			}
		}
	}
}
