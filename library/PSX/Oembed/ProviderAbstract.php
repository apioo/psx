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

	public function onGet()
	{
		$this->doDiscover();
	}

	public function onPost()
	{
		throw new Exception('Method not allowed', 405);
	}

	protected function doDiscover()
	{
		$url       = $this->getParameter('url', Validate::TYPE_STRING, array(new Filter\Length(3, 512), new Filter\Url()), 'Url');
		$maxWidth  = $this->getParameter('maxwidth', Validate::TYPE_INTEGER, array(), 'Max width');
		$maxHeight = $this->getParameter('maxHeight', Validate::TYPE_INTEGER, array(), 'Max height');

		$url  = new Url($url);
		$type = $this->onRequest($url, $maxWidth, $maxHeight);

		if($type instanceof TypeAbstract)
		{
			$this->response->setStatusCode(200);

			if($this->isWriter(WriterInterface::XML))
			{
				$this->response->setHeader('Content-Type', 'text/xml+oembed');
			}
			else if($this->isWriter(WriterInterface::JSON))
			{
				$this->response->setHeader('Content-Type', 'application/json+oembed');
			}

			$this->setBody($type);
		}
		else
		{
			throw new Exception('Url not found', 404);
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

