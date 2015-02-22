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

namespace PSX\Api\View\Generator;

use PSX\Api\View;
use PSX\Api\View\GeneratorAbstract;
use PSX\Data\SchemaInterface;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;
use PSX\Data\Schema\Generator\JsonSchema as JsonSchemaGenerator;
use PSX\Json;

/**
 * JsonSchema
 *
 * @see     http://tools.ietf.org/html/draft-zyp-json-schema-04
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class JsonSchema extends GeneratorAbstract
{
	protected $targetNamespace;

	public function __construct($targetNamespace)
	{
		$this->targetNamespace = $targetNamespace;
	}

	public function generate(View $view)
	{
		$definitions = array();
		$properties  = array();

		foreach($view as $key => $schema)
		{
			$generator = new JsonSchemaGenerator($this->targetNamespace);
			$data      = json_decode($generator->generate($schema), true);

			unset($data['$schema']);
			unset($data['id']);

			if(isset($data['definitions']))
			{
				$definitions = array_merge($definitions, $data['definitions']);

				unset($data['definitions']);
			}
			
			if(isset($data['properties']))
			{
				$properties[$this->getPrefix($key)] = $data;
			}
		}

		$result = array(
			'$schema'     => JsonSchemaGenerator::SCHEMA,
			'id'          => $this->targetNamespace,
			'type'        => 'object',
			'definitions' => $definitions,
			'properties'  => $properties,
		);

		return Json::encode($result, JSON_PRETTY_PRINT);
	}
}
