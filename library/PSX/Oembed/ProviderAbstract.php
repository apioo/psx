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
abstract class PSX_Oembed_ProviderAbstract extends PSX_Module_ApiAbstract
{
	public static $mime = 'application/json+oembed';

	public function onGet()
	{
		$validate = new PSX_Validate();
		$get      = new PSX_Input_Get($validate);

		$url       = $get->url('string', array(new PSX_Filter_Length(3, 512), new PSX_Filter_Url()), 'url', 'Url');
		$maxWidth  = $get->maxwidth('integer', array(new PSX_Filter_Length(8, 4096)), 'maxwidth', 'Max width');
		$maxHeight = $get->maxheight('integer', array(new PSX_Filter_Length(8, 4096)), 'maxheight', 'Max height');
		//$format    = $get->format('string', array(new PSX_Filter_InArray(array('json', 'xml'))), 'format', 'Format', false);

		if(!$validate->hasError())
		{
			$url  = new PSX_Url($url);
			$type = $this->onRequest($url, $maxWidth, $maxHeight);

			if($type instanceof PSX_Oembed_TypeAbstract)
			{
				// @todo send correct content type
				// header('Content-type: application/json+oembed');
				// header('Content-type: text/xml+oembed');

				$this->setResponse($type);
			}
			else
			{
				throw new PSX_Oembed_Exception('Url not found', 404);
			}
		}
		else
		{
			throw new PSX_Oembed_Exception($validate->getLastError());
		}
	}

	public function onPost()
	{
		throw new PSX_Exception('Method not allowed', 405);
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
	abstract public function onRequest(PSX_Url $url, $maxWidth, $maxHeight);

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

