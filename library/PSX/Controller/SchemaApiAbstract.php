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

namespace PSX\Controller;

use PSX\Api\DocumentationInterface;
use PSX\Api\DocumentedInterface;
use PSX\Api\Version;
use PSX\Api\Resource;
use PSX\Api\InvalidVersionException;
use PSX\ControllerAbstract;
use PSX\Data\Object;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Data\SchemaInterface;
use PSX\Data\Schema\Validator;
use PSX\Data\Schema\Assimilator;
use PSX\Http\Exception as StatusCode;

/**
 * SchemaApiAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link	http://phpsx.org
 */
abstract class SchemaApiAbstract extends ApiAbstract implements DocumentedInterface
{
	/**
	 * @Inject
	 * @var PSX\Data\Schema\Assimilator
	 */
	protected $schemaAssimilator;

	/**
	 * @Inject
	 * @var PSX\Data\Schema\SchemaManager
	 */
	protected $schemaManager;

	/**
	 * @Inject
	 * @var PSX\Api\ResourceListingInterface
	 */
	protected $resourceListing;

	/**
	 * @var PSX\Data\Record
	 */
	protected $queryParameters;

	/**
	 * @var PSX\Data\Record
	 */
	protected $pathParameters;

	/**
	 * @var PSX\Api\Resource
	 */
	protected $resource;

	/**
	 * @var PSX\Api\Version
	 */
	protected $version;

	public function onLoad()
	{
		$doc = $this->getDocumentation();

		$this->version  = $this->getVersion($doc);
		$this->resource = $this->getResource($doc, $this->version);

		$this->pathParameters = $this->schemaAssimilator->assimilate($this->resource->getPathParameters(), $this->uriFragments);
	}

	public function onGet()
	{
		if(!$this->resource->hasMethod('GET'))
		{
			throw new StatusCode\MethodNotAllowedException('Method is not allowed', $this->resource->getAllowedMethods());
		}

		$method = $this->resource->getMethod('GET');

		$this->queryParameters = $this->schemaAssimilator->assimilate($method->getQueryParameters(), $this->request->getQueryParams());

		$response = $this->doGet($this->version);

		$this->setBody($this->schemaAssimilator->assimilate($method->getResponse(200), $response));
	}

	public function onPost()
	{
		if(!$this->resource->hasMethod('POST'))
		{
			throw new StatusCode\MethodNotAllowedException('Method is not allowed', $this->resource->getAllowedMethods());
		}

		$method = $this->resource->getMethod('POST');

		$this->queryParameters = $this->schemaAssimilator->assimilate($method->getQueryParameters(), $this->request->getQueryParams());

		$record   = $method->hasRequest() ? $this->import($method->getRequest()) : new Record();
		$response = $this->doCreate($record, $this->version);

		if($method->hasResponse(200))
		{
			$this->setResponseCode(201);
			$this->setBody($this->schemaAssimilator->assimilate($method->getResponse(200), $response));
		}
		else
		{
			$this->setResponseCode(204);
			$this->setBody('');
		}
	}

	public function onPut()
	{
		if(!$this->resource->hasMethod('PUT'))
		{
			throw new StatusCode\MethodNotAllowedException('Method is not allowed', $this->resource->getAllowedMethods());
		}

		$method = $this->resource->getMethod('PUT');

		$this->queryParameters = $this->schemaAssimilator->assimilate($method->getQueryParameters(), $this->request->getQueryParams());

		$record   = $method->hasRequest() ? $this->import($method->getRequest()) : new Record();
		$response = $this->doUpdate($record, $this->version);

		if($method->hasResponse(200))
		{
			$this->setResponseCode(200);
			$this->setBody($this->schemaAssimilator->assimilate($method->getResponse(200), $response));
		}
		else
		{
			$this->setResponseCode(204);
			$this->setBody('');
		}
	}

	public function onDelete()
	{
		if(!$this->resource->hasMethod('DELETE'))
		{
			throw new StatusCode\MethodNotAllowedException('Method is not allowed', $this->resource->getAllowedMethods());
		}

		$method = $this->resource->getMethod('DELETE');

		$this->queryParameters = $this->schemaAssimilator->assimilate($method->getQueryParameters(), $this->request->getQueryParams());

		$record   = $method->hasRequest() ? $this->import($method->getRequest()) : new Record();
		$response = $this->doDelete($record, $this->version);

		if($method->hasResponse(200))
		{
			$this->setResponseCode(200);
			$this->setBody($this->schemaAssimilator->assimilate($method->getResponse(200), $response));
		}
		else
		{
			$this->setResponseCode(204);
			$this->setBody('');
		}
	}

	/**
	 * Returns the GET response
	 *
	 * @param PSX\Api\Version $version
	 * @return array|PSX\Data\RecordInterface
	 */
	abstract protected function doGet(Version $version);

	/**
	 * Returns the POST response
	 *
	 * @param PSX\Data\RecordInterface $record
	 * @param PSX\Api\Version $version
	 * @return array|PSX\Data\RecordInterface
	 */
	abstract protected function doCreate(RecordInterface $record, Version $version);

	/**
	 * Returns the PUT response
	 *
	 * @param PSX\Data\RecordInterface $record
	 * @param PSX\Api\Version $version
	 * @return array|PSX\Data\RecordInterface
	 */
	abstract protected function doUpdate(RecordInterface $record, Version $version);

	/**
	 * Returns the DELETE response
	 *
	 * @param PSX\Data\RecordInterface $record
	 * @param PSX\Api\Version $version
	 * @return array|PSX\Data\RecordInterface
	 */
	abstract protected function doDelete(RecordInterface $record, Version $version);

	protected function getResource(DocumentationInterface $doc, Version $version)
	{
		if(!$doc->hasResource($version->getVersion()))
		{
			throw new StatusCode\NotAcceptableException('Version is not available');
		}

		$resource = $doc->getResource($version->getVersion());

		if($resource->isActive())
		{
		}
		else if($resource->isDeprecated())
		{
			$this->response->addHeader('Warning', '199 PSX "Version v' . $version->getVersion() . ' is deprecated"');
		}
		else if($resource->isClosed())
		{
			throw new StatusCode\GoneException('Version v' . $version->getVersion() . ' is not longer supported');
		}

		return $resource;
	}

	protected function getVersion(DocumentationInterface $doc)
	{
		if($doc->isVersionRequired())
		{
			$accept  = $this->getHeader('Accept');
			$matches = array();

			preg_match('/^application\/vnd\.([a-z.-_]+)\.v([\d]+)\+([a-z]+)$/', $accept, $matches);

			$name    = isset($matches[1]) ? $matches[1] : null;
			$version = isset($matches[2]) ? $matches[2] : null;
			$format  = isset($matches[3]) ? $matches[3] : null;

			if($version !== null)
			{
				return new Version((int) $version);
			}
			else
			{
				// it is strongly recommended that clients specify an explicit
				// version but forcing that with an exception is not a good user
				// experience therefore we use the latest version if nothing is 
				// specified
				return new Version($doc->getLatestVersion());

				//throw new StatusCode\UnsupportedMediaTypeException('Requires an Accept header containing an explicit version');
			}
		}
		else
		{
			return new Version(1);
		}
	}
}
