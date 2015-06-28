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

namespace PSX\Template;

use PSX\Config;
use PSX\TemplateInterface;
use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * Twig
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Twig implements TemplateInterface
{
	protected $config;
	protected $dir;
	protected $file;
	protected $data = array();

	public function __construct(Config $config)
	{
		$this->config = $config;
	}

	public function setDir($dir)
	{
		$this->dir = $dir;
	}

	public function getDir()
	{
		return $this->dir;
	}

	public function set($file)
	{
		$this->file = $file;
	}

	public function get()
	{
		return $this->file;
	}

	public function hasFile()
	{
		return !empty($this->file);
	}

	public function getFile()
	{
		return $this->dir != null ? $this->dir . '/' . $this->file : $this->file;
	}

	public function isFileAvailable()
	{
		return is_file($this->getFile());
	}

	public function isAbsoluteFile()
	{
		return is_file($this->file);
	}

	public function assign($key, $value)
	{
		$this->data[$key] = $value;
	}

	public function transform()
	{
		$loader = new Twig_Loader_Filesystem($this->dir);
		$twig   = new Twig_Environment($loader, array(
			'cache' => $this->config['psx_path_cache'],
			'debug' => $this->config['psx_debug'],
		));

		return $twig->render($this->file, $this->data);
	}
}

