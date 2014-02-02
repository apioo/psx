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
use PSX\Http\Response;
use PSX\Loader\Location;
use Symfony\Component\DependencyInjection\ContainerInterface;
use RuntimeException;

/**
 * ControllerAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ControllerAbstract implements ControllerInterface
{
	protected $container;
	protected $location;
	protected $request;
	protected $response;
	protected $uriFragments;
	protected $stage;
	protected $config;

	protected $_parameter;
	protected $_requestReader;

	public function __construct(ContainerInterface $container, Location $location, Request $request, Response $response, array $uriFragments)
	{
		$this->container    = $container;
		$this->location     = $location;
		$this->request      = $request;
		$this->response     = $response;
		$this->uriFragments = $uriFragments;
		$this->stage        = 0x3F;
		$this->config       = $container->get('config');
	}

	public function getStage()
	{
		return $this->stage;
	}

	public function getRequestFilter()
	{
		return array();
	}

	public function getResponseFilter()
	{
		return array();
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

	public function processResponse()
	{
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
		return $this->location->getPath();
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
	 * Forwards the request to another controller
	 *
	 * @param string $path
	 */
	protected function forward($path)
	{
		// sets the path of the url and loads the controller
		if($this->request->getUrl()->getPath() != $path)
		{
			$this->request->getUrl()->setPath($path);
		}
		else
		{
			throw new InvalidArgumentException('Cant forward the request to the same path');
		}

		$this->container->get('loader')->load($this->request, $this->response);
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

	/**
	 * Sets the http response code
	 *
	 * @param integer $code
	 */
	protected function setResponseCode($code)
	{
		$this->response->setStatusCode($code);
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
		if($key === null)
		{
			return $this->request->getHeaders();
		}
		else
		{
			return $this->request->getHeader($key);
		}
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
