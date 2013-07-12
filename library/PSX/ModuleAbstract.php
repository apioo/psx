<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX;

use PSX\Data\NotFoundException;
use PSX\Data\ReaderFactory;
use PSX\Dependency;
use PSX\Loader\Location;

/**
 * ModuleAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ModuleAbstract
{
	protected $container;
	protected $location;
	protected $basePath;
	protected $uriFragments = array();

	protected $base;
	protected $config;

	protected $parameter;
	protected $body;

	public function __construct($container, Location $location, $basePath, array $uriFragments)
	{
		$this->container    = $container;
		$this->location     = $location;
		$this->basePath     = $basePath;
		$this->uriFragments = $uriFragments;

		$this->base         = $container->get('base');
		$this->config       = $container->get('config');
	}

	/**
	 * Returns an array of request filters wich are applied on the current 
	 * request
	 *
	 * @return array<PSX\Dispatch\RequestFilterInterface>
	 */
	public function getRequestFilter()
	{
		return array();
	}

	/**
	 * Returns an array of response filters wich are applied on the response
	 *
	 * @return array<PSX\Dispatch\ResponseFilterInterface>
	 */
	public function getResponseFilter()
	{
		return array();
	}

	/**
	 * If the called method starts with "get" the matching service from the di 
	 * container is returned else null
	 *
	 * @return object
	 */
	public function __call($name, $args)
	{
		if(substr($name, 0, 3) == 'get')
		{
			$service = lcfirst(substr($name, 3));

			if($this->container->has($service))
			{
				return $this->container->get($service);
			}

			return null;
		}
	}

	public function onLoad()
	{
	}

	public function onGet()
	{
	}

	public function onPost()
	{
	}

	public function onPut()
	{
	}

	public function onDelete()
	{
	}

	/**
	 * Is called so that the module can process the content that means i.e. load
	 * a template or output the content. The $content contains all output wich 
	 * was captured through output buffering
	 *
	 * @param string $content
	 * @return string
	 */
	public function processResponse($content)
	{
		return $content;
	}

	protected function getContainer()
	{
		return $this->container;
	}

	protected function getLocation()
	{
		return $this->location;
	}

	protected function getBase()
	{
		return $this->base;
	}

	protected function getBasePath()
	{
		return $this->basePath;
	}

	protected function getUriFragments($key = null)
	{
		if($key !== null)
		{
			return isset($this->uriFragments[$key]) ? $this->uriFragments[$key] : null;
		}
		else
		{
			return $this->uriFragments;
		}
	}

	protected function getConfig()
	{
		return $this->config;
	}

	/**
	 * Sets the http response code
	 *
	 * @param integer $code
	 */
	protected function setResponseCode($code)
	{
		Base::setResponseCode($code);
	}

	/**
	 * Forwards the request to another controller
	 *
	 * @param string $path
	 */
	protected function forward($path)
	{
		$this->container->get('loader')->load($path, $this->base->getRequest());
	}

	protected function getMethod()
	{
		return Base::getRequestMethod();
	}

	protected function getUrl()
	{
		return $this->base->getRequest()->getUrl();
	}

	protected function getHeader($key = null)
	{
		return Base::getRequestHeader($key);
	}

	protected function getParameter()
	{
		if($this->parameter === null)
		{
			$parameter = $this->getUrl()->getParams();

			$this->parameter = new Input($parameter, $this->container->get('validate'));
		}

		return $this->parameter;
	}

	/**
	 * Returns the result data from the reader wich is in most cases an array or 
	 * an SimpleXMLElement if the Content-Type is application/xml
	 *
	 * @param integer $readerType
	 * @param boolean $returnResult
	 * @return mixed
	 */
	protected function getBody($readerType = null)
	{
		if($this->body === null)
		{
			try
			{
				$this->body = $this->getRequest($readerType)->getData();
			}
			catch(NotFoundException $e)
			{
			}
		}

		return $this->body;
	}

	/**
	 * Returns an PSX\Data\ReaderResult object depending of the $readerType.
	 * If the reader type is not set the content-type of the request is used to 
	 * get the best fitting reader. You can import the reader result into an 
	 * record with the import method
	 *
	 * @param integer $readerType
	 * @return PSX\Data\ReaderResult
	 */
	protected function getRequest($readerType = null)
	{
		// find best reader type
		if($readerType === null)
		{
			$contentType = Base::getRequestHeader('Content-Type');
			$readerType  = ReaderFactory::getReaderTypeByContentType($contentType);
		}

		// get reader
		$reader = ReaderFactory::getReader($readerType);

		if($reader === null)
		{
			throw new NotFoundException('Could not find fitting data reader');
		}

		// try to read request
		return $reader->read($this->base->getRequest());
	}
}

