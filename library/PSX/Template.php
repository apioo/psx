<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

use PSX\Data\RecordInterface;
use PSX\Template\ErrorException;
use PSX\Template\FallbackGenerator;
use PSX\Template\GeneratorInterface;

/**
 * Template
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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

