<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

/**
 * Template
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Template
{
	protected $data = array();

	protected $dir;
	protected $file;
	protected $path = false;

	private $config;

	public function __construct(Config $config)
	{
		$this->config = $config;

		$this->setDir(PSX_PATH_TEMPLATE . '/' . $this->config['psx_template_dir']);
		$this->set($this->config['psx_template_default']);
	}

	public function setDir($dir)
	{
		if(is_dir($dir))
		{
			$this->dir = $dir;

			return true;
		}

		return false;
	}

	public function getDir()
	{
		return $this->dir;
	}

	public function set($file)
	{
		if(!empty($file))
		{
			$path = $this->dir . '/' . $file;

			if(is_file($path))
			{
				$this->file = $file;
				$this->path = $path;

				return true;
			}
		}

		return false;
	}

	public function getFile()
	{
		return $this->file;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getData()
	{
		return $this->data;
	}

	public function assign($key, $value)
	{
		if(!isset($this->data[$key]))
		{
			$this->data[$key] = $value;
		}
		else
		{
			throw new Exception('Key ' . $key . ' already set');
		}
	}

	public function transform()
	{
		// check whether path is set
		if(empty($this->path))
		{
			throw new Exception('No template set');
		}

		// predefined vars
		$config   = $this->config;
		$self     = isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']) ? $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] : $_SERVER['PHP_SELF'];
		$url      = $this->config['psx_url'] . '/' . $this->config['psx_dispatch'];
		$location = $this->dir;
		$render   = round(microtime(true) - $GLOBALS['psx_benchmark'], 6);
		$base     = parse_url($this->config['psx_url'], PHP_URL_PATH);

		// populate the data vars in the scope of the template
		extract($this->data, EXTR_SKIP);

		// parse template
		ob_start();

		require_once($this->path);

		$html = ob_get_clean();

		return $html;
	}
}
