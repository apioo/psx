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

namespace PSX;

use DOMDocument;
use InvalidArgumentException;
use PSX\Controller\Behaviour;
use PSX\Data\ReaderInterface;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Data\Writer;
use PSX\Data\WriterInterface;
use PSX\Dispatch\Filter\ControllerExecutor;
use PSX\Http\Exception as StatusCode;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use PSX\Http\StreamInterface;
use PSX\Loader\Context;
use SimpleXMLElement;

/**
 * ControllerAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class ControllerAbstract implements ControllerInterface, ApplicationStackInterface
{
	use Behaviour\AccessorTrait;
	use Behaviour\HttpTrait;
	use Behaviour\ImporterTrait;
	use Behaviour\RedirectTrait;

	/**
	 * @var \PSX\Http\RequestInterface
	 */
	protected $request;

	/**
	 * @var \PSX\Http\ResponseInterface
	 */
	protected $response;

	/**
	 * @var \PSX\Loader\Context
	 */
	protected $context;

	/**
	 * @var array
	 */
	protected $uriFragments;

	/**
	 * @Inject
	 * @var \PSX\Config
	 */
	protected $config;

	/**
	 * @Inject
	 * @var \PSX\Validate
	 */
	protected $validate;

	/**
	 * @Inject
	 * @var \PSX\Data\ReaderFactory
	 */
	protected $readerFactory;

	/**
	 * @Inject
	 * @var \PSX\Data\WriterFactory
	 */
	protected $writerFactory;

	private $_responseWritten = false;

	/**
	 * @param \PSX\Http\RequestInterface $request
	 * @param \PSX\Http\ResponseInterface $response
	 * @param \PSX\Loader\Context $context
	 */
	public function __construct(RequestInterface $request, ResponseInterface $response, Context $context = null)
	{
		$this->request      = $request;
		$this->response     = $response;
		$this->context      = $context ?: new Context();
		$this->uriFragments = $this->context->get(Context::KEY_FRAGMENT) ?: array();
	}

	public function getApplicationStack()
	{
		return array_merge(
			$this->getPreFilter(), 
			array(new ControllerExecutor($this, $this->context)), 
			$this->getPostFilter()
		);
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
		$this->context->set(Context::KEY_SUPPORTED_WRITER, $this->getSupportedWriter());
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

	public function onOptions()
	{
	}

	public function onPost()
	{
	}

	public function onPut()
	{
	}

	public function onTrace()
	{
	}

	public function processResponse()
	{
		$body = $this->response->getBody();

		if($body->tell() == 0 && !$this->_responseWritten)
		{
			$this->setBody(new Record());
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
	 * Method to set an response body
	 *
	 * @param mixed $data
     * @param string $writerType
	 */
	protected function setBody($data, $writerType = null)
	{
		if($this->_responseWritten)
		{
			// we have already written a response
			return;
		}

		if(is_array($data))
		{
			$this->setResponse(new Record('record', $data), $writerType);
		}
		else if($data instanceof \stdClass)
		{
			$this->setResponse(new Record('record', (array) $data), $writerType);
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
		else if($data instanceof StreamInterface)
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
	 * Checks whether the preferred reader is an instance of the reader class
	 *
	 * @param string $readerClass
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
	 * Configures the writer
	 *
	 * @param \PSX\Data\WriterInterface $writer
	 */
	protected function configureWriter(WriterInterface $writer)
	{
		if($writer instanceof Writer\TemplateAbstract)
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
			if(!$writer->getRequestMethod())
			{
				$writer->setRequestMethod($this->request->getMethod());
			}
		}
		else if($writer instanceof Writer\Jsonp)
		{
			if(!$writer->getCallbackName())
			{
				$writer->setCallbackName($this->getParameter('callback'));
			}
		}
	}

	/**
	 * Can be overriden by an controller to return the formats which are 
	 * supported. All following controllers will have the same supported writers 
	 * as the origin controller. If null gets returned every available format is 
	 * supported otherwise it must return an array containing writer class names
	 *
	 * @return array
	 */
	protected function getSupportedWriter()
	{
		return $this->context->get(Context::KEY_SUPPORTED_WRITER);
	}

	/**
	 * Writes the $record with the writer $writerType or depending on the get 
	 * parameter format or of the mime type of the Accept header
	 *
	 * @param \PSX\Data\RecordInterface $record
	 * @param string $writerType
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

		// the response may have multiple presentations based on the Accept
		// header field
		if(!$this->response->hasHeader('Vary'))
		{
			$this->response->setHeader('Vary', 'Accept');
		}

		// set content type header if not available
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
	 * Returns the best reader for the given content type or throws an 
	 * unsupported media exception
	 *
	 * @param string $readerType
	 * @return \PSX\Data\ReaderInterface
	 */
	private function getRequestReader($readerType = null)
	{
		if($readerType === null)
		{
			$reader = $this->getPreferredReader();
		}
		else
		{
			$reader = $this->readerFactory->getReaderByInstance($readerType);
		}

		if(!$reader instanceof ReaderInterface)
		{
			throw new StatusCode\UnsupportedMediaTypeException('Could not find fitting data reader');
		}

		return $reader;
	}

	/**
	 * Returns the fitting response writer
	 *
     * @param string $writerType
	 * @return \PSX\Data\WriterInterface
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
			$writer = $this->writerFactory->getDefaultWriter($this->getSupportedWriter());
		}

		if(!$writer instanceof WriterInterface)
		{
			throw new StatusCode\NotAcceptableException('Could not find fitting data writer');
		}

		return $writer;
	}

	/**
	 * Returns the reader depending on the content type
	 *
	 * @return \PSX\Data\ReaderInterface
	 */
	private function getPreferredReader()
	{
		return $this->readerFactory->getReaderByContentType($this->request->getHeader('Content-Type'));
	}

	/**
	 * Returns the writer wich gets used if no writer was explicit selected
	 *
	 * @return \PSX\Data\WriterInterface
	 */
	private function getPreferredWriter()
	{
		$parameters = $this->request->getUri()->getParameters();
		$format     = isset($parameters['format']) ? $parameters['format'] : null;

		if(!empty($format))
		{
			return $this->writerFactory->getWriterByFormat($format, $this->getSupportedWriter());
		}
		else
		{
			return $this->writerFactory->getWriterByContentType($this->request->getHeader('Accept'), $this->getSupportedWriter());
		}
	}
}
