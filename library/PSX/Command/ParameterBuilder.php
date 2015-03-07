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

namespace PSX\Command;

/**
 * ParameterBuilder
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ParameterBuilder
{
	protected $parameters;

	public function __construct()
	{
		$this->parameters = new Parameters();
	}

	public function setDescription($description)
	{
		$this->parameters->setDescription($description);

		return $this;
	}

	public function addOption($name, $type, $description = null)
	{
		$this->parameters->add(new Parameter($name, $type, $description));

		return $this;
	}

	public function getParameters()
	{
		return $this->parameters;
	}
}
