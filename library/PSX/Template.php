<?php
/*
 *  $Id: Template.php 532 2012-07-09 20:32:26Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

/**
 * PSX_Template
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Template
 * @version    $Revision: 532 $
 */
class PSX_Template
{
	public $data = array();

	public $dir;
	public $file;
	public $path = false;

	private $config;

	public function __construct(PSX_Config $config)
	{
		$this->config = $config;

		$this->dir($this->config['psx_template_dir']);
		$this->set($this->config['psx_template_default']);
	}

	public function dir($dir)
	{
		if(is_dir(PSX_PATH_TEMPLATE . '/' . $dir))
		{
			$this->dir = $dir;

			return true;
		}

		return false;
	}

	public function set($file)
	{
		if(!empty($file))
		{
			$path = PSX_PATH_TEMPLATE . '/' . $this->dir . '/' . $file;

			if(PSX_File::exists($path))
			{
				$this->file = $file;
				$this->path = $path;

				return true;
			}
		}

		return false;
	}

	public function assign($key, $value)
	{
		if(!isset($this->data[$key]))
		{
			$this->data[$key] = $value;
		}
		else
		{
			throw new PSX_Template_Exception('Key ' . $key . ' already set');
		}
	}

	public function transform()
	{
		// check whether path is set
		if(empty($this->path))
		{
			throw new PSX_Template_Exception('No template set');
		}

		// predefined vars
		$config   = $this->config;
		$self     = isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']) ? $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] : $_SERVER['PHP_SELF'];
		$url      = $this->config['psx_url'] . '/' . $this->config['psx_dispatch'];
		$location = PSX_PATH_TEMPLATE . '/' . $this->dir;
		$render   = round(microtime(true) - $GLOBALS['psx_benchmark'], 6);
		$base     = parse_url($this->config['psx_url'], PHP_URL_PATH);

		// populate the data vars in the scope of the template
		extract($this->data, EXTR_SKIP);

		// parse template
		ob_start();

		require_once($this->path);

		$html = ob_get_contents();

		ob_end_clean();


		return $html;
	}
}
