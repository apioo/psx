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

namespace PSX\Controller\Behaviour;

use DOMDocument;
use PSX\Http\Exception as StatusCode;
use InvalidArgumentException;
use Psr\Http\Message\StreamableInterface;
use PSX\Data\NotFoundException;
use PSX\Data\ReaderFactory;
use PSX\Data\ReaderInterface;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Data\Record\ImporterInterface;
use PSX\Data\Writer;
use PSX\Data\WriterInterface;
use SimpleXMLElement;

/**
 * BodyTrait
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link	http://phpsx.org
 */
trait BodyTrait
{
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

	private $_responseWritten = false;

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
	 * Configures the writer
	 *
	 * @param PSX\Data\WriterInterface $writer
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
			$writer->setRequestMethod($this->request->getMethod());
		}
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
	 * @return PSX\Data\ReaderInterface
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
		$parameters = $this->request->getQueryParams();
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
