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

namespace PSX;

use BadMethodCallException;
use DOMDocument;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamableInterface;
use PSX\Data\Accessor;
use PSX\Data\NotFoundException;
use PSX\Data\ReaderFactory;
use PSX\Data\RecordInterface;
use PSX\Data\Record\ImporterInterface;
use PSX\Data\Writer;
use PSX\Data\WriterInterface;
use PSX\Data\Record;
use PSX\Data\TransformerInterface;
use PSX\Dependency;
use PSX\Http\Exception\TemporaryRedirectException;
use PSX\Http\Stream\TempStream;
use PSX\Loader\Location;
use PSX\Url;
use PSX\Validate;
use SimpleXMLElement;
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
	/**
	 * @var PSX\Loader\Location
	 */
	protected $location;

	/**
	 * @var Psr\Http\Message\RequestInterface
	 */
	protected $request;

	/**
	 * @var Psr\Http\Message\ResponseInterface
	 */
	protected $response;

	/**
	 * @var array
	 */
	protected $uriFragments;

	/**
	 * @var integer
	 */
	protected $stage;

	/**
	 * @Inject
	 * @var PSX\Config
	 */
	protected $config;

	/**
	 * @Inject
	 * @var PSX\Validate
	 */
	protected $validate;

	/**
	 * @Inject
	 * @var PSX\Loader
	 */
	protected $loader;

	/**
	 * @Inject
	 * @var PSX\Loader\ReverseRouter
	 */
	protected $reverseRouter;

	/**
	 * @Inject
	 * @var PSX\Data\ReaderFactory
	 */
	protected $readerFactory;

	/**
	 * @Inject
	 * @var PSX\Data\WriterFactory
	 */
	protected $writerFactory;

	/**
	 * @Inject
	 * @var PSX\Data\Importer
	 */
	protected $importer;

	/**
	 * @Inject
	 * @var PSX\Data\Extractor
	 */
	protected $extractor;

	private $_responseWritten = false;

	private $_accessor;

	/**
	 * @param PSX\Loader\Location $location
	 * @param Psr\Http\Message\RequestInterface $request
	 * @param Psr\Http\Message\ResponseInterface $request
	 */
	public function __construct(Location $location, RequestInterface $request, ResponseInterface $response)
	{
		$this->location     = $location;
		$this->request      = $request;
		$this->response     = $response;
		$this->uriFragments = $location->getParameter(Location::KEY_FRAGMENT);
		$this->stage        = 0x3F;
	}

	public function getStage()
	{
		return $this->stage;
	}

	public function getPreFilter()
	{
		return array();
	}

	public function getPostFilter()
	{
		return array();
	}

	public function onLoad()
	{
	}

	public function onDelete()
	{
	}

	public function onGet()
	{
	}

	public function onHead()
	{
	}

	public function onPost()
	{
	}

	public function onPut()
	{
	}

	/**
	 * Method which gets called after the controller method was called. In case 
	 * we have not written any response we write an empty response so that the 
	 * writer can set an response
	 */
	public function processResponse()
	{
		$body = $this->response->getBody();

		if($body !== null && $body->tell() == 0 && !$this->_responseWritten)
		{
			$this->setResponse(new Record());
		}
	}

	/**
	 * Returns an specific uri fragment
	 *
	 * @param string $key
	 * @return string
	 */
	protected function getUriFragment($key)
	{
		return isset($this->uriFragments[$key]) ? $this->uriFragments[$key] : null;
	}

	/**
	 * Forwards the request to another controller
	 *
	 * @param string $source
	 * @param array $parameters
	 */
	protected function forward($source, array $parameters = array())
	{
		$path = $this->reverseRouter->getPath($source, $parameters);

		$this->request->setMethod('GET');
		$this->request->getUrl()->setPath($path);

		$this->loader->load($this->request, $this->response);
	}

	/**
	 * Throws an redirect exception which sends an Location header. If source is 
	 * not an url the reverse router is used to determine the url
	 *
	 * @param string $source
	 * @param array $parameters
	 * @param integer $code
	 */
	protected function redirect($source, array $parameters = array(), $code = 307)
	{
		if($source instanceof Url)
		{
			$url = $source->getUrl();
		}
		else if(filter_var($source, FILTER_VALIDATE_URL))
		{
			$url = $source;
		}
		else
		{
			$url = $this->reverseRouter->getUrl($source, $parameters);
		}

		throw new TemporaryRedirectException($url);
	}

	/**
	 * Sets the http response status code
	 *
	 * @param integer $code
	 */
	protected function setResponseCode($code)
	{
		$this->response->setStatusCode($code);
	}

	/**
	 * Returns the request method. Note the X-HTTP-Method-Override header 
	 * replaces the actually request method if available
	 *
	 * @return string
	 */
	protected function getMethod()
	{
		return $this->request->getMethod();
	}

	/**
	 * Returns the request url
	 *
	 * @return PSX\Url
	 */
	protected function getUrl()
	{
		return $this->request->getUrl();
	}

	/**
	 * Returns an specific request header
	 *
	 * @param string $key
	 * @return string
	 */
	protected function getHeader($key)
	{
		return $this->request->getHeader($key);
	}

	/**
	 * Returns an parameter from the query fragment of the request url
	 *
	 * @param string $key
	 * @param string $type
	 * @param array $filter
	 * @param string $title
	 * @param boolean $required
	 * @return mixed
	 */
	protected function getParameter($key, $type = Validate::TYPE_STRING, array $filter = array(), $title = null, $required = true)
	{
		if($this->getUrl()->issetParam($key))
		{
			return $this->validate->apply($this->getUrl()->getParam($key), $type, $filter, $title, $required);
		}
		else
		{
			return null;
		}
	}

	/**
	 * Returns all available request parameters
	 *
	 * @return array
	 */
	protected function getParameters()
	{
		return $this->getUrl()->getParams();
	}

	/**
	 * Returns the result of the reader for the request
	 *
	 * @param string $readerType
	 * @return mixed
	 */
	protected function getBody($readerType = null)
	{
		return $this->getRequestReader($readerType)->read($this->request);
	}

	/**
	 * Returns an accessor object with that you can easily access values from
	 * the request body
	 *
	 * @param PSX\Data\TransformerInterface $transformer
	 * @param string $readerType
	 * @return PSX\Data\Accessor
	 */
	protected function getAccessor(TransformerInterface $transformer = null, $readerType = null)
	{
		if($this->_accessor === null)
		{
			$data     = $this->extractor->extract($this->request, $transformer, $readerType);
			$accessor = new Accessor($this->validate, $data);

			$this->_accessor = $accessor;
		}

		return $this->_accessor;
	}

	/**
	 * Method to set an response body
	 *
	 * @param mixed $data
	 */
	protected function setBody($data, $writerType = null)
	{
		if($this->_responseWritten)
		{
			throw new RuntimeException('Response was already written');
		}

		if(is_array($data))
		{
			$this->setResponse(new Record('record', $data), $writerType);
		}
		else if($data instanceof RecordInterface)
		{
			$this->setResponse($data, $writerType);
		}
		else if($data instanceof DOMDocument)
		{
			if(!$this->response->hasHeader('Content-Type'))
			{
				$this->response->setHeader('Content-Type', 'application/xml');
			}

			$this->response->getBody()->write($data->saveXML());
		}
		else if($data instanceof SimpleXMLElement)
		{
			if(!$this->response->hasHeader('Content-Type'))
			{
				$this->response->setHeader('Content-Type', 'application/xml');
			}

			$this->response->getBody()->write($data->asXML());
		}
		else if(is_string($data))
		{
			$this->response->getBody()->write($data);
		}
		else if($data instanceof StreamableInterface)
		{
			$this->response->setBody($data);
		}
		else
		{
			throw new InvalidArgumentException('Invalid data type');
		}

		$this->_responseWritten = true;
	}

	/**
	 * Imports data from the current request into an record
	 *
	 * @param mixed $source
	 * @param PSX\Data\TransformerInterface $transformer
	 * @param string $readerType
	 * @return PSX\Data\RecordInterface
	 */
	protected function import($source, TransformerInterface $transformer = null, $readerType = null)
	{
		return $this->importer->import($source, $this->request, $transformer, $readerType);
	}

	/**
	 * Checks whether the preferred reader is an instance of the reader class
	 *
	 * @param string $writerClass
	 * @return boolean
	 */
	protected function isReader($readerClass)
	{
		return $this->getPreferredReader() instanceof $readerClass;
	}

	/**
	 * Checks whether the preferred writer is an instance of the writer class
	 *
	 * @param string $writerClass
	 * @return boolean
	 */
	protected function isWriter($writerClass)
	{
		return $this->getPreferredWriter() instanceof $writerClass;
	}

	/**
	 * Returns the formats which are supported by this controller. If null gets
	 * returned every available format is supported otherwise it must return
	 * an array containing writer class names
	 *
	 * @return array
	 */
	protected function getSupportedWriter()
	{
		return null;
	}

	/**
	 * Configures the writer
	 *
	 * @param PSX\Data\WriterInterface $writer
	 */
	protected function configureWriter(WriterInterface $writer)
	{
		if($writer instanceof Writer\Html)
		{
			if(!$writer->getBaseDir())
			{
				$writer->setBaseDir(PSX_PATH_LIBRARY);
			}

			if(!$writer->getControllerClass())
			{
				$writer->setControllerClass(get_class($this));
			}
		}
		else if($writer instanceof Writer\Soap)
		{
			$writer->setRequestMethod($this->request->getMethod());
		}
	}

	/**
	 * Writes the $record with the writer $writerType or depending on the get 
	 * parameter format or of the mime type of the Accept header
	 *
	 * @param PSX\Data\RecordInterface $record
	 * @param string $writerType
	 * @param integer $code
	 * @return void
	 */
	private function setResponse(RecordInterface $record, $writerType = null)
	{
		// find best writer type
		$writer = $this->getResponseWriter($writerType);

		// set writer specific settings
		$this->configureWriter($writer);

		// write the response
		$response = $writer->write($record);

		// send content type header if not sent
		if(!$this->response->hasHeader('Content-Type'))
		{
			$contentType = $writer->getContentType();

			if($contentType !== null)
			{
				$this->response->setHeader('Content-Type', $contentType);
			}
		}

		// for iframe file uploads we need an text/html content type header even 
		// if we want serve json content. If all browsers support the FormData
		// api we can send file uploads per ajax but for now we use this hack.
		// Note do not rely on this param it will be removed as soon as possible
		if(isset($_GET['htmlMime']))
		{
			$this->response->setHeader('Content-Type', 'text/html');
		}

		$this->response->getBody()->write($response);
	}

	/**
	 * Returns the best reader for the given content type or the default reader
	 * from the factory
	 *
	 * @param string $readerType
	 * @return PSX\Data\ReaderInterface
	 */
	private function getRequestReader($readerType = null)
	{
		// find best reader type
		if($readerType === null)
		{
			$reader = $this->getPreferredReader();
		}
		else
		{
			$reader = $this->readerFactory->getReaderByInstance($readerType);
		}

		if($reader === null)
		{
			$reader = $this->readerFactory->getDefaultReader();
		}

		if($reader === null)
		{
			throw new NotFoundException('Could not find fitting data reader', 415);
		}

		return $reader;
	}

	/**
	 * Returns the fitting response writer
	 *
	 * @return PSX\Data\WriterInterface
	 */
	private function getResponseWriter($writerType = null)
	{
		if($writerType === null)
		{
			$writer = $this->getPreferredWriter();
		}
		else
		{
			$writer = $this->writerFactory->getWriterByInstance($writerType);
		}

		if($writer === null)
		{
			$writer = $this->writerFactory->getDefaultWriter();
		}

		if(!$writer instanceof WriterInterface)
		{
			throw new NotFoundException('Could not find fitting data writer');
		}

		return $writer;
	}

	/**
	 * Returns the reader depending on the content type
	 *
	 * @return PSX\Data\ReaderInterface
	 */
	private function getPreferredReader()
	{
		return $this->readerFactory->getReaderByContentType($this->request->getHeader('Content-Type'));
	}

	/**
	 * Returns the writer wich gets used if no writer was explicit selected
	 *
	 * @return PSX\Data\WriterInterface
	 */
	private function getPreferredWriter()
	{
		$format = $this->request->getUrl()->getParam('format');

		if(!empty($format))
		{
			return $this->writerFactory->getWriterByFormat($format, $this->getSupportedWriter());
		}
		else
		{
			$contentType = $this->request->getHeader('Accept');

			return $this->writerFactory->getWriterByContentType($contentType, $this->getSupportedWriter());
		}
	}
}
