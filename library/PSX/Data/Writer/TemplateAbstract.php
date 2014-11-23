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

namespace PSX\Data\Writer;

use PSX\Data\RecordInterface;
use PSX\Data\WriterInterface;
use PSX\Loader\ReverseRouter;
use PSX\TemplateInterface;

/**
 * Abstract class to facilitate an template engine to produce the output
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
