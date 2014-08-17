<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Controller;

use PSX\ControllerAbstract;
use PSX\Data\RecordInterface;
use PSX\Data\SchemaInterface;
use PSX\Data\Schema\ApiDocumentation;

/**
 * SchemaApiAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link	http://phpsx.org
 */
abstract class SchemaApiAbstract extends ApiAbstract
{
	/**
	 * @Inject
	 * @var PSX\Data\Schema\Assimilator
	 */
	protected $schemaAssimilator;

	public function onGet()
	{
		$response = $this->doGet();

		list($responseSchema) = $this->getSchemaDocumentation()->get(ApiDocumentation::METHOD_GET);

		if($responseSchema instanceof SchemaInterface)
		{
			$this->setBody($this->schemaAssimilator->assimilate($response, $responseSchema));
		}
		else
		{
			$this->setBody($response);
		}
	}

	public function onPost()
	{
		list($requestSchema, $responseSchema) = $this->getSchemaDocumentation()->get(ApiDocumentation::METHOD_POST);

		if(!$requestSchema instanceof SchemaInterface)
		{
			throw new \Exception('Method not allowed', 405);
		}

		$record   = $this->import($requestSchema);
		$response = $this->doCreate($record);

		if($responseSchema instanceof SchemaInterface)
		{
			$this->setBody($this->schemaAssimilator->assimilate($response, $responseSchema));
		}
		else
		{
			$this->setBody($response);
		}
	}

	public function onPut()
	{
		list($requestSchema, $responseSchema) = $this->getSchemaDocumentation()->get(ApiDocumentation::METHOD_PUT);

		if(!$requestSchema instanceof SchemaInterface)
		{
			throw new \Exception('Method not allowed', 405);
		}

		$record   = $this->import($requestSchema);
		$response = $this->doUpdate($record);

		if($responseSchema instanceof SchemaInterface)
		{
			$this->setBody($this->schemaAssimilator->assimilate($response, $responseSchema));
		}
		else
		{
			$this->setBody($response);
		}
	}

	public function onDelete()
	{
		list($requestSchema, $responseSchema) = $this->getSchemaDocumentation()->get(ApiDocumentation::METHOD_DELETE);

		if(!$requestSchema instanceof SchemaInterface)
		{
			throw new \Exception('Method not allowed', 405);
		}

		$record   = $this->import($requestSchema);
		$response = $this->doDelete($record);

		if($responseSchema instanceof SchemaInterface)
		{
			$this->setBody($this->schemaAssimilator->assimilate($response, $responseSchema));
		}
		else
		{
			$this->setBody($response);
		}
	}

	protected function doGet()
	{
	}

	protected function doCreate(RecordInterface $record)
	{
	}

	protected function doUpdate(RecordInterface $record)
	{
	}

	protected function doDelete(RecordInterface $record)
	{
	}

	/**
	 * Returns an object which contains all available schema definitions for 
	 * this api. The method is public so that we can get the object for 
	 * automatic api doc generation
	 *
	 * @return PSX\Data\Schema\ApiDocumentation
	 */
	abstract public function getSchemaDocumentation();
}
