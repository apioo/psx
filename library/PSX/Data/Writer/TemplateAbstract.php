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

namespace PSX\Data\Writer;

use PSX\Data\RecordInterface;
use PSX\Data\WriterInterface;
use PSX\Loader\ReverseRouter;
use PSX\TemplateInterface;

/**
 * Abstract class to facilitate an template engine to produce the output
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class TemplateAbstract implements WriterInterface
{
	protected $template;
	protected $reverseRouter;
	protected $className;

	public function __construct(TemplateInterface $template, ReverseRouter $reverseRouter)
	{
		$this->template      = $template;
		$this->reverseRouter = $reverseRouter;
		$this->baseDir       = PSX_PATH_LIBRARY;
	}

	public function setBaseDir($baseDir)
	{
		$this->baseDir = $baseDir;
	}

	public function getBaseDir()
	{
		return $this->baseDir;
	}

	public function setControllerClass($className)
	{
		$this->className = $className;
	}

	public function getControllerClass()
	{
		return $this->className;
	}

	public function write(RecordInterface $record)
	{
		// set default template if no template is set
		$class = str_replace('\\', '/', $this->className);
		$path  = $this->baseDir . '/' . strstr($class, '/Application/', true) . '/Resource';

		if(!$this->template->hasFile())
		{
			$ext  = $this->getFileExtension();
			$file = substr(strstr($class, 'Application'), 12);
			$file = $this->underscore($file) . '.' . $ext;

			$this->template->setDir($path);
			$this->template->set($file);
		}
		else
		{
			$this->template->setDir(!$this->template->fileExists() ? $path : null);
		}

		// assign default values
		$self   = isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']) ? $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] : $_SERVER['PHP_SELF'];
		$render = round(microtime(true) - $GLOBALS['psx_benchmark'], 6);

		$this->template->assign('self', htmlspecialchars($self));
		$this->template->assign('url', $this->reverseRouter->getDispatchUrl());
		$this->template->assign('base', $this->reverseRouter->getBasePath());
		$this->template->assign('render', $render);
		$this->template->assign('location', $path);
		$this->template->assign('router', $this->reverseRouter);

		// assign data
		$fields = $record->getRecordInfo()->getFields();

		foreach($fields as $key => $value)
		{
			$this->template->assign($key, $value);
		}

		return $this->template->transform();
	}

	/**
	 * Returns the file extension which is used by the template file. The file
	 * extension must not include a leading dot
	 *
	 * @return string
	 */
	abstract public function getFileExtension();

	protected function underscore($word)
	{
		return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $word));
	}
}
