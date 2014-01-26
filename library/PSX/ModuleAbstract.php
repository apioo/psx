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

use InvalidArgumentException;
use PSX\Data\NotFoundException;
use PSX\Data\ReaderFactory;
use PSX\Data\RecordInterface;
use PSX\Data\Record\ImporterInterface;
use PSX\Dependency;
use PSX\Http\Request;
use PSX\Loader\Location;
use Symfony\Component\DependencyInjection\ContainerInterface;
use RuntimeException;

/**
 * ModuleAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ModuleAbstract
{
	const CALL_REQUEST_FILTER   = 0x1;
	const CALL_ONLOAD           = 0x2;
	const CALL_REQUEST_METHOD   = 0x4;
	const CALL_METHOD           = 0x8;
	const CALL_PROCESS_RESPONSE = 0x10;
	const CALL_RESPONSE_FILTER  = 0x20;

	protected $container;
	protected $location;
	protected $basePath;
	protected $uriFragments = array();
	protected $request;
	protected $stage;

	protected $base;
	protected $config;

	protected $_parameter;
	protected $_requestReader;

	public function __construct(ContainerInterface $container, Location $location, $basePath, array $uriFragments, Request $request)
	{
		$this->container    = $container;
		$this->location     = $location;
		$this->basePath     = $basePath;
		$this->uriFragments = $uriFragments;
		$this->request      = $request;
		$this->stage        = 0x3F;

		$this->base         = $container->get('base');
		$this->config       = $container->get('config');
	}

	/**
	 * The module can control the behaviour wich method should be called by the
	 * loader. In most cases you do not need to modify this behaviour
	 *
	 * @return integer
	 */
	public function getStage()
	{
		return $this->stage;
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

			throw new InvalidArgumentException('Service ' . $service . ' not available');
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
		$this->container->get('loader')->load($path, $this->request);
	}

	/**
	 * Sends an location header and exits the application. If path is not an url
	 * the complete url will be created with the base url from the config
	 *
	 * @param string $path
	 * @param integer $code
	 */
	protected function redirect($url, $code = 307)
	{
		if(!filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED))
		{
			$url = $this->config['psx_url'] . '/' . $this->config['psx_dispatch'] . ltrim($url, '/');
		}

		Base::setResponseCode($code);
		header('Location: ' . $url);
		exit;
	}

	protected function getMethod()
	{
		return $this->request->getMethod();
	}

	protected function getUrl()
	{
		return $this->request->getUrl();
	}

	protected function getHeader($key = null)
	{
		return Base::getRequestHeader($key);
	}

	protected function getParameter()
	{
		if($this->_parameter === null)
		{
			$parameter = $this->getUrl()->getParams();

			$this->_parameter = new Input($parameter, $this->container->get('validate'));
		}

		return $this->_parameter;
	}

	/**
	 * Returns the result of the reader 
	 *
	 * @param string $readerType
	 * @return mixed
	 */
	protected function getBody($readerType = null)
	{
		return $this->getRequestReader($readerType)->read($this->request);
	}

	/**
	 * Uses the default importer from the request reader to import arbitrary 
	 * data into an record
	 *
	 * @param mixed $record
	 * @param string $readerType
	 * @return PSX\Data\RecordInterface
	 */
	protected function import($record, $readerType = null)
	{
		$reader   = $this->getRequestReader($readerType);
		$importer = $reader->getDefaultImporter();

		if($importer instanceof ImporterInterface)
		{
			return $importer->import($record, $reader->read($this->request));
		}
		else
		{
			throw new RuntimeException('Reader has no default importer');
		}
	}

	/**
	 * Returns the best reader for the given content type or the default reader
	 * from the factory
	 *
	 * @param string $readerType
	 * @return PSX\Data\ReaderInterface
	 */
	protected function getRequestReader($readerType = null)
	{
		if($this->_requestReader === null)
		{
			// find best reader type
			if($readerType === null)
			{
				$reader = $this->container->get('readerFactory')->getReaderByContentType($this->request->getHeader('Content-Type'));
			}
			else
			{
				$reader = $this->container->get('readerFactory')->getReaderByInstance($readerType);
			}

			if($reader === null)
			{
				$reader = $this->container->get('readerFactory')->getDefaultReader();
			}

			if($reader === null)
			{
				throw new NotFoundException('Could not find fitting data reader');
			}

			$this->_requestReader = $reader;
		}

		return $this->_requestReader;
	}
}
