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

use PSX\Api\DocumentationInterface;
use PSX\Api\DocumentedInterface;
use PSX\Api\Version;
use PSX\Api\InvalidVersionException;
use PSX\ControllerAbstract;
use PSX\Data\RecordInterface;
use PSX\Data\SchemaInterface;
use PSX\Http\Exception as StatusCode;

/**
 * SchemaApiAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link	http://phpsx.org
 */
abstract class SchemaApiAbstract extends ApiAbstract implements DocumentedInterface
{
	/**
	 * @Inject
	 * @var PSX\Data\Schema\Assimilator
	 */
	protected $schemaAssimilator;

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

		$this->setBody($this->schemaAssimilator->assimilate($response, $view->getGetResponse()));
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

		$record   = $this->import($view->getPostRequest());
		$response = $this->doCreate($record, $version);

		if($view->hasPostResponse())
		{
			$this->setResponseCode(201);
			$this->setBody($this->schemaAssimilator->assimilate($response, $view->getPostResponse()));
		}
		else
		{
			$this->setResponseCode(204);
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

		$record   = $this->import($view->getPutRequest());
		$response = $this->doUpdate($record, $version);

		if($view->hasPutResponse())
		{
			$this->setResponseCode(200);
			$this->setBody($this->schemaAssimilator->assimilate($response, $view->getPutResponse()));
		}
		else
		{
			$this->setResponseCode(204);
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

		$record   = $this->import($view->getDeleteRequest());
		$response = $this->doDelete($record, $version);

		if($view->hasDeleteResponse())
		{
			$this->setResponseCode(200);
			$this->setBody($this->schemaAssimilator->assimilate($response, $view->getDeleteResponse()));
		}
		else
		{
			$this->setResponseCode(204);
		}
	}

	protected function doGet(Version $version)
	{
	}

	protected function doCreate(RecordInterface $record, Version $version)
	{
	}

	protected function doUpdate(RecordInterface $record, Version $version)
	{
	}

	protected function doDelete(RecordInterface $record, Version $version)
	{
	}

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

		return $view;
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
				throw new StatusCode\UnsupportedMediaTypeException('Requires an content type containing an explicit version');
			}
		}
		else
		{
			return new Version(1);
		}
	}
}
