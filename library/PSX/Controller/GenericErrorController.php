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

use DOMDocument;
use PSX\Controller\ViewAbstract;
use PSX\Http;
use PSX\Loader\Location;
use PSX\Template\ErrorException;

/**
 * GenericErrorController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class GenericErrorController extends ViewAbstract
{
	const CONTEXT_SIZE = 4;

	/**
	 * @Inject
	 * @var Psr\Log\LoggerInterface
	 */
	protected $logger;

	public function onLoad()
	{
		$exception = $this->location->getParameter(Location::KEY_EXCEPTION);

		if($exception instanceof \Exception)
		{
			$this->logger->error($exception->getMessage());
		}
	}

	public function processResponse()
	{
		$exception = $this->location->getParameter(Location::KEY_EXCEPTION);

		if($exception instanceof \Exception)
		{
			$this->handleException($exception);
		}
	}

	protected function handleException(\Exception $exception)
	{
		// set status code
		$code = $this->response->getStatusCode();

		if($code === null && isset(Http::$codes[$exception->getCode()]))
		{
			$code = $exception->getCode();
		}
		else if($code === null)
		{
			$code = 500;
		}

		$this->response->setStatusCode($code);

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
			$title   = 'Internal Server Error';
			$message = 'The server encountered an internal error and was unable to complete your request.';
			$trace   = null;
			$context = null;
		}

		$data = array(
			'success' => false,
			'title'   => $title,
			'message' => $message,
			'trace'   => $trace,
			'context' => $context,
		);

		$this->setBody($data);
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
			return __DIR__ . '/default_error.tpl';
		}
	}

	protected function underscore($word)
	{
		return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $word));
	}
}
