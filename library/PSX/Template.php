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

use PSX\Data\RecordInterface;
use PSX\Template\ErrorException;
use PSX\Template\FallbackGenerator;
use PSX\Template\GeneratorInterface;

/**
 * Template
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Template implements TemplateInterface
{
	protected $dir;
	protected $file;
	protected $data = array();

	protected $generator;

	/**
	 * The fallback generator is used if the template engine has no template
	 * file
	 *
	 * @param PSX\Template\GeneratorInterface $fallbackGenerator
	 */
	public function __construct(GeneratorInterface $fallbackGenerator = null)
	{
		$this->generator = $fallbackGenerator ?: new FallbackGenerator();
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

	public function fileExists()
	{
		return is_file($this->file);
	}

	public function getFile()
	{
		return $this->dir != null ? $this->dir . '/' . $this->file : $this->file;
	}

	public function assign($key, $value)
	{
		$this->data[$key] = $value;
	}

	public function transform()
	{
		$file = $this->getFile();

		if(!is_file($file))
		{
			// if we use the fallback template we dont want to expose the default 
			// template values only the actual data set by the user
			$reservedKeys = array(
				'self'     => null, 
				'url'      => null, 
				'base'     => null, 
				'render'   => null, 
				'location' => null, 
				'router'   => null,
			);

			$html = $this->generator->generate(array_diff_key($this->data, $reservedKeys));
		}
		else
		{
			// parse template
			try
			{
				ob_start();

				includeTemplateScope($this->data, $file);

				$html = ob_get_clean();
			}
			catch(\Exception $e)
			{
				throw new ErrorException($e->getMessage(), $e, $this->getFile(), ob_get_clean());
			}
		}

		return $html;
	}
}

/**
 * Includes the file without exposing the properties of the template object
 */
function includeTemplateScope(array $data, $file)
{
	// populate the data vars in the scope of the template
	extract($data, EXTR_SKIP);

	// include file
	require_once($file);
}

