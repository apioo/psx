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
use PSX\Api\View;
use PSX\Api\InvalidVersionException;
use PSX\ControllerAbstract;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Data\SchemaInterface;
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

	public function onGet()
	{
		$doc     = $this->getDocumentation();
		$version = $this->getVersion($doc);
		$view    = $this->getView($doc, $version);

		if(!$view->hasGet())
		{
			throw new StatusCode\MethodNotAllowedException('Method is not allowed', $view->getAllowedMethods());
		}

		$response = $this->doGet($version);

		$this->setBody($this->schemaAssimilator->assimilate($view->getGetResponse(), $response));
	}

	public function onPost()
	{
		$doc     = $this->getDocumentation();
		$version = $this->getVersion($doc);
		$view    = $this->getView($doc, $version);

		if(!$view->hasPost())
		{
			throw new StatusCode\MethodNotAllowedException('Method is not allowed', $view->getAllowedMethods());
		}

		$record   = $view->hasPostRequest() ? $this->import($view->getPostRequest()) : new Record();
		$response = $this->doCreate($record, $version);

		if($view->hasPostResponse())
		{
			$this->setResponseCode(201);
			$this->setBody($this->schemaAssimilator->assimilate($view->getPostResponse(), $response));
		}
		else
		{
			$this->setResponseCode(204);
			$this->setBody('');
		}
	}

	public function onPut()
	{
		$doc     = $this->getDocumentation();
		$version = $this->getVersion($doc);
		$view    = $this->getView($doc, $version);

		if(!$view->hasPut())
		{
			throw new StatusCode\MethodNotAllowedException('Method is not allowed', $view->getAllowedMethods());
		}

		$record   = $view->hasPutRequest() ? $this->import($view->getPutRequest()) : new Record();
		$response = $this->doUpdate($record, $version);

		if($view->hasPutResponse())
		{
			$this->setResponseCode(200);
			$this->setBody($this->schemaAssimilator->assimilate($view->getPutResponse(), $response));
		}
		else
		{
			$this->setResponseCode(204);
			$this->setBody('');
		}
	}

	public function onDelete()
	{
		$doc     = $this->getDocumentation();
		$version = $this->getVersion($doc);
		$view    = $this->getView($doc, $version);

		if(!$view->hasDelete())
		{
			throw new StatusCode\MethodNotAllowedException('Method is not allowed', $view->getAllowedMethods());
		}

		$record   = $view->hasDeleteRequest() ? $this->import($view->getDeleteRequest()) : new Record();
		$response = $this->doDelete($record, $version);

		if($view->hasDeleteResponse())
		{
			$this->setResponseCode(200);
			$this->setBody($this->schemaAssimilator->assimilate($view->getDeleteResponse(), $response));
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

	protected function getView(DocumentationInterface $doc, Version $version)
	{
		if(!$doc->hasView($version->getVersion()))
		{
			throw new StatusCode\NotAcceptableException('Version is not available');
		}

		$view = $doc->getView($version->getVersion());

		if($view->isActive())
		{
		}
		else if($view->isDeprecated())
		{
			$this->response->addHeader('Warning', '199 PSX "Version v' . $version->getVersion() . ' is deprecated"');
		}
		else if($view->isClosed())
		{
			throw new StatusCode\GoneException('Version v' . $version->getVersion() . ' is not longer supported');
		}

		return new View\Facade($view);
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
				// its is strongly recommended that clients specify an explicit
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
