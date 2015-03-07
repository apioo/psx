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

use DOMDocument;
use PSX\Controller\ViewAbstract;
use PSX\Data\ExceptionRecord;
use PSX\DisplayException;
use PSX\Http;
use PSX\Loader\Context;
use PSX\Template\ErrorException;

/**
 * ErrorController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ErrorController extends ViewAbstract
{
	const CONTEXT_SIZE = 4;

	public function processResponse()
	{
		$exception = $this->context->get(Context::KEY_EXCEPTION);

		if($exception instanceof \Exception)
		{
			$this->handleException($exception);
		}
	}

	protected function handleException(\Exception $exception)
	{
		// set error template
		$class = str_replace('\\', '/', get_class($this));

		if(strpos($class, '/Application/') !== false)
		{
			$path = PSX_PATH_LIBRARY . '/' . strstr($class, '/Application/', true) . '/Resource';
			$file = substr(strstr($class, 'Application'), 12);
			$file = $this->underscore($file) . '.tpl';

			if(!is_file($path . '/' . $file))
			{
				$this->template->set($this->getFallbackTemplate());
			}
		}
		else
		{
			$this->template->set($this->getFallbackTemplate());
		}

		// build message
		if($this->config['psx_debug'] === true)
		{
			if($exception instanceof ErrorException)
			{
				$exception = $exception->getOriginException();
			}

			$title   = get_class($exception);
			$message = $exception->getMessage() . ' in ' . $exception->getFile() . ' on line ' . $exception->getLine();
			$trace   = $exception->getTraceAsString();
			$context = '';

			if(is_file($exception->getFile()))
			{
				$offset = $exception->getLine() - (self::CONTEXT_SIZE + 1);
				$length = (self::CONTEXT_SIZE * 2) + 1;
				$length = $offset < 0 ? $length + $offset : $length;
				$offset = $offset < 0 ? 0 : $offset;

				$lines  = file($exception->getFile());
				$lines  = array_slice($lines, $offset, $length);

				foreach($lines as $number => $line)
				{
					$lineNo = $offset + $number + 1;

					if($lineNo == $exception->getLine())
					{
						$context.= '<b>' . str_pad($lineNo, 4) . htmlspecialchars($line) . '</b>';
					}
					else
					{
						$context.= str_pad($lineNo, 4) . htmlspecialchars($line);
					}
				}
			}
		}
		else
		{
			// if we have an display exception we can use the error message else
			// we hide the message with an general error message
			if($exception instanceof DisplayException)
			{
				$message = $exception->getMessage();
			}
			else
			{
				$message = 'The server encountered an internal error and was unable to complete your request.';
			}

			$title   = 'Internal Server Error';
			$trace   = null;
			$context = null;
		}

		$record = new ExceptionRecord();
		$record->setSuccess(false);
		$record->setTitle($title);
		$record->setMessage($message);
		$record->setTrace($trace);
		$record->setContext($context);

		$this->setBody($record);
	}

	/**
	 * Returns the fallback template which is used if the template has no file
	 * and the controller is not in an application structure
	 *
	 * @return string|Closure
	 */
	protected function getFallbackTemplate()
	{
		if(isset($this->config['psx_error_template']))
		{
			return $this->config['psx_error_template'];
		}
		else
		{
			return __DIR__ . '/Resource/error_controller.tpl';
		}
	}

	protected function underscore($word)
	{
		return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $word));
	}
}
