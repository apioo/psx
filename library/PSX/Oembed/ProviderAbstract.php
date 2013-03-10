<?php
/*
 *  $Id: ProviderAbstract.php 586 2012-08-15 21:29:03Z k42b3.x@googlemail.com $
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

namespace PSX\Oembed;

use PSX\Exception;
use PSX\Filter;
use PSX\Input\Get;
use PSX\Module\ApiAbstract;
use PSX\Url;
use PSX\Validate;

/**
 * PSX_Oembed_ProviderAbstract
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Oembed
 * @version    $Revision: 586 $
 */
abstract class ProviderAbstract extends ApiAbstract
{
	public static $mime = 'application/json+oembed';

	public function onGet()
	{
		$validate = new Validate();
		$get      = new Get($validate);

		$url       = $get->url('string', array(new Filter\Length(3, 512), new Filter\Url()), 'url', 'Url');
		$maxWidth  = $get->maxwidth('integer', array(new Filter\Length(8, 4096)), 'maxwidth', 'Max width');
		$maxHeight = $get->maxheight('integer', array(new Filter\Length(8, 4096)), 'maxheight', 'Max height');
		//$format    = $get->format('string', array(new Filter\InArray(array('json', 'xml'))), 'format', 'Format', false);

		if(!$validate->hasError())
		{
			$url  = new Url($url);
			$type = $this->onRequest($url, $maxWidth, $maxHeight);

			if($type instanceof TypeAbstract)
			{
				// @todo send correct content type
				// header('Content-type: application/json+oembed');
				// header('Content-type: text/xml+oembed');

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

	public function onPost()
	{
		throw new Exception('Method not allowed', 405);
	}

	/**
	 * Is called if an oembed request was made must return an
	 * PSX_Oembed_TypeAbstract object. If no PSX_Oembed_TypeAbstract object is
	 * returned an 404 status code is send
	 *
	 * @param PSX_Url $url
	 * @param integer $maxWidth
	 * @param integer $maxHeight
	 * @return PSX_Oembed_TypeAbstract
	 */
	abstract public function onRequest(Url $url, $maxWidth, $maxHeight);

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

