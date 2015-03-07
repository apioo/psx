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

namespace PSX\Api\Documentation;

use PSX\Api\DocumentationInterface;
use PSX\Api\View;

/**
 * Version
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Version implements DocumentationInterface
{
	protected $views = array();
	protected $description;

	public function __construct($description = null)
	{
		$this->description = $description;
	}

	public function addView($version, View $view)
	{
		$this->views[$version] = $view;
	}

	public function hasView($version)
	{
		return isset($this->views[$version]);
	}

	public function getView($version)
	{
		return isset($this->views[$version]) ? $this->views[$version] : null;
	}

	public function getViews()
	{
		return $this->views;
	}

	public function getLatestVersion()
	{
		if(count($this->views) > 0)
		{
			return max(array_keys($this->views));
		}
		else
		{
			return 1;
		}
	}

	public function isVersionRequired()
	{
		return true;
	}

	public function getDescription()
	{
		return $this->description;
	}
}
