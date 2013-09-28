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
		$config   = $this->getConfig();
		$template = $this->getTemplate();

		// set template dir
		$template->setDir(PSX_PATH_LIBRARY);

		// set default template if no template is set
		if(!$template->hasFile())
		{
			$class = str_replace('\\', '/', $this->location->getClass()->getName());
			$file  = strtolower(substr(strstr($class, 'Application'), 12)) . '.tpl';
			$path  = strstr($class, 'Application', true) . 'Resource';

			$template->set($path . '/' . $file);
		}
		else
		{
			$file  = $template->get();
			$path  = strstr($class, 'Application', true) . 'Resource';
		}

		// assign default values
		$self   = isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']) ? $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] : $_SERVER['PHP_SELF'];
		$url    = $config['psx_url'] . '/' . $config['psx_dispatch'];
		$base   = parse_url($config['psx_url'], PHP_URL_PATH);
		$render = round(microtime(true) - $GLOBALS['psx_benchmark'], 6);

		$template->assign('config', $config);
		$template->assign('self', htmlspecialchars($self));
		$template->assign('url', $url);
		$template->assign('base', $base);
		$template->assign('render', $render);
		$template->assign('location', PSX_PATH_LIBRARY . '/' . $path);

		if(empty($content))
		{
			if(!($response = $template->transform()))
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

