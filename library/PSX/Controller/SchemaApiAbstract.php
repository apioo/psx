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
use PSX\Data\Schema\Documentation;

/**
 * SchemaApiAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link	http://phpsx.org
 */
abstract class SchemaApiAbstract extends ApiAbstract implements SchemaDocumentedInterface
{
	/**
	 * @Inject
	 * @var PSX\Data\Schema\Assimilator
	 */
	protected $schemaAssimilator;

	public function onGet()
	{
		$response = $this->doGet();
		$schema   = $this->getSchemaDocumentation();

		if($schema->hasResponse(Documentation::METHOD_GET))
		{
			$this->setBody($this->schemaAssimilator->assimilate($response, $schema->getResponse(Documentation::METHOD_GET)));
		}
		else
		{
			$this->setBody($response);
		}
	}

	public function onPost()
	{
		$schema = $this->getSchemaDocumentation();

		if(!$schema->hasRequest(Documentation::METHOD_POST))
		{
			throw new \Exception('Method not allowed', 405);
		}

		$record   = $this->import($schema->getRequest(Documentation::METHOD_POST));
		$response = $this->doCreate($record);

		if($schema->hasResponse(Documentation::METHOD_POST))
		{
			$this->setBody($this->schemaAssimilator->assimilate($response, $schema->getResponse(Documentation::METHOD_POST)));
		}
		else
		{
			$this->setBody($response);
		}
	}

	public function onPut()
	{
		$schema = $this->getSchemaDocumentation();

		if(!$schema->hasRequest(Documentation::METHOD_PUT))
		{
			throw new \Exception('Method not allowed', 405);
		}

		$record   = $this->import($schema->getRequest(Documentation::METHOD_PUT));
		$response = $this->doUpdate($record);

		if($schema->hasResponse(Documentation::METHOD_PUT))
		{
			$this->setBody($this->schemaAssimilator->assimilate($response, $schema->getResponse(Documentation::METHOD_PUT)));
		}
		else
		{
			$this->setBody($response);
		}
	}

	public function onDelete()
	{
		$schema = $this->getSchemaDocumentation();

		if(!$schema->hasRequest(Documentation::METHOD_DELETE))
		{
			throw new \Exception('Method not allowed', 405);
		}

		$record   = $this->import($schema->getRequest(Documentation::METHOD_DELETE));
		$response = $this->doDelete($record);

		if($schema->hasResponse(Documentation::METHOD_DELETE))
		{
			$this->setBody($this->schemaAssimilator->assimilate($response, $schema->getResponse(Documentation::METHOD_DELETE)));
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
}
