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

namespace PSX\Template;

use PSX\Config;
use PSX\TemplateInterface;

/**
 * Smarty
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Smarty implements TemplateInterface
{
	protected $smarty;
	protected $dir;
	protected $file;

	public function __construct(Config $config)
	{
		$this->smarty = new \Smarty();
		$this->smarty->setTemplateDir($config['psx_path_cache'] . '/templates');
		$this->smarty->setCompileDir($config['psx_path_cache'] . '/templates_c');
		$this->smarty->setCacheDir($config['psx_path_cache'] . '/cache');
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
		$this->smarty->assign($key, $value);
	}

	public function transform()
	{
		if($this->dir != null)
		{
			$this->smarty->setTemplateDir($this->dir);
		}

		return $this->smarty->fetch($this->file);
	}
}
