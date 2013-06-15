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

namespace PSX\Module;

use PSX\Base;
use PSX\Exception;
use PSX\Dependency\View;
use PSX\Dispatch\RequestFilter\GzipEncode;
use PSX\ModuleAbstract;

/**
 * ViewAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ViewAbstract extends ModuleAbstract
{
	public function getDependencies()
	{
		return new View($this->getConfig());
	}

	public function getResponseFilter()
	{
		$filter = array();

		if($this->config['psx_gzip'] === true)
		{
			$filter[] = new GzipEncode();
		}

		return $filter;
	}

	public function processResponse($content)
	{
		// assign default values
		$config = $this->getConfig();
		$self   = isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']) ? $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] : $_SERVER['PHP_SELF'];
		$url    = $config['psx_url'] . '/' . $config['psx_dispatch'];
		$dir    = PSX_PATH_TEMPLATE . '/' . $config['psx_template_dir'];
		$base   = parse_url($config['psx_url'], PHP_URL_PATH);
		$render = round(microtime(true) - $GLOBALS['psx_benchmark'], 6);

		$this->getTemplate()->assign('config', $config);
		$this->getTemplate()->assign('self', $self);
		$this->getTemplate()->assign('url', $url);
		$this->getTemplate()->assign('location', $dir);
		$this->getTemplate()->assign('base', $base);
		$this->getTemplate()->assign('render', $render);

		// set template dir
		$this->getTemplate()->setDir($dir);

		// set default template if no template is set
		if(!$this->getTemplate()->hasFile())
		{
			$file = str_replace('\\', '/', $this->location->getClass()->getName() . '.tpl');

			$this->getTemplate()->set($file);
		}

		if(empty($content))
		{
			if(!($response = $this->getTemplate()->transform()))
			{
				throw new Exception('Error while transforming template');
			}

			return $response;
		}
		else
		{
			return $content;
		}
	}
}

