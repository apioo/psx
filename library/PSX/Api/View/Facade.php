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

namespace PSX\Api\View;

use PSX\Api\View;

/**
 * Class to provide a better interface to access the view values
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
