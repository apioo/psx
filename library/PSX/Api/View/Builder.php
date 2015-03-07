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
use PSX\Data\SchemaInterface;

/**
 * Builder class to create a new API view
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Builder
{
	protected $view;

	public function __construct($status = View::STATUS_ACTIVE, $path = null)
	{
		$this->view = new View($status, $path);
	}

	public function setGet(SchemaInterface $responseSchema = null, SchemaInterface $parameterSchema = null)
	{
		$this->view->set(View::METHOD_GET | View::TYPE_RESPONSE, $responseSchema);
		$this->view->set(View::METHOD_GET | View::TYPE_PARAMETER, $parameterSchema);
	}

	public function setPost(SchemaInterface $requestSchema = null, SchemaInterface $responseSchema = null)
	{
		$this->view->set(View::METHOD_POST | View::TYPE_REQUEST, $requestSchema);
		$this->view->set(View::METHOD_POST | View::TYPE_RESPONSE, $responseSchema);
	}

	public function setPut(SchemaInterface $requestSchema = null, SchemaInterface $responseSchema = null)
	{
		$this->view->set(View::METHOD_PUT | View::TYPE_REQUEST, $requestSchema);
		$this->view->set(View::METHOD_PUT | View::TYPE_RESPONSE, $responseSchema);
	}

	public function setDelete(SchemaInterface $requestSchema = null, SchemaInterface $responseSchema = null)
	{
		$this->view->set(View::METHOD_DELETE | View::TYPE_REQUEST, $requestSchema);
		$this->view->set(View::METHOD_DELETE | View::TYPE_RESPONSE, $responseSchema);
	}

	public function getView()
	{
		return $this->view;
	}
}
