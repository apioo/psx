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

namespace PSX\Oembed;

use PSX\Data\WriterInterface;
use PSX\Exception;
use PSX\Filter;
use PSX\Input\Get;
use PSX\Controller\ApiAbstract;
use PSX\Url;
use PSX\Validate;

/**
 * ProviderAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ProviderAbstract extends ApiAbstract
{
	public static $mime = 'application/json+oembed';

	/**
	 * @httpMethod GET
	 * @path /
	 */
	public function doGet()
	{
		$this->doDiscover();
	}

	/**
	 * @httpMethod POST
	 * @path /
	 */
	public function doPost()
	{
		throw new Exception('Method not allowed', 405);
	}

	protected function doDiscover()
	{
		$url       = $this->request->getUrl()->getParam('url');
		$maxWidth  = $this->request->getUrl()->getParam('maxwidth');
		$maxHeight = $this->request->getUrl()->getParam('maxheight');

		$validate  = new Validate();
		$url       = $validate->apply($url, Validate::TYPE_STRING, array(new Filter\Length(3, 512), new Filter\Url()), 'url', 'Url');
		$maxWidth  = $validate->apply($maxWidth, Validate::TYPE_INTEGER, array(), 'maxwidth', 'Max width');
		$maxHeight = $validate->apply($maxHeight, Validate::TYPE_INTEGER, array(), 'maxheight', 'Max height');

		if(!$validate->hasError())
		{
			$url  = new Url($url);
			$type = $this->onRequest($url, $maxWidth, $maxHeight);

			if($type instanceof TypeAbstract)
			{
				if($this->isWriter(WriterInterface::XML))
				{
					$this->response->setHeader('Content-Type', 'text/xml+oembed');
				}
				else if($this->isWriter(WriterInterface::JSON))
				{
					$this->response->setHeader('Content-Type', 'application/json+oembed');
				}

				$this->setResponse($type);
			}
			else
			{
				throw new Exception('Url not found', 404);
			}
		}
		else
		{
			throw new Exception($validate->getLastError());
		}
	}

	/**
	 * Is called if an oembed request was made must return an
	 * PSX\Oembed\TypeAbstract object. If no PSX\Oembed\TypeAbstract object is
	 * returned an 404 status code is send
	 *
	 * @param PSX\Url $url
	 * @param integer $maxWidth
	 * @param integer $maxHeight
	 * @return PSX\Oembed\TypeAbstract
	 */
	abstract protected function onRequest(Url $url, $maxWidth, $maxHeight);

	/**
	 * Returns the html link tag wich can be used to discover the oembed source
	 *
	 * @return string
	 */
	public static function link($title, $href)
	{
		return '<link rel="alternate" type="' . self::$mime . '" title="' . $title . '" href="' . $href . '" />';
	}
}

