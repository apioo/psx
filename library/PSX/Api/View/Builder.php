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
use PSX\Data\SchemaInterface;

/**
 * Builder class to create a new API view
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
