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
 * Urn
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://www.ietf.org/rfc/rfc2141.txt
 */
class Urn extends Uri
{
	public function __construct($uri)
	{
		$parts = self::parse($uri);

		$this->setScheme($parts['scheme']);
		$this->setAuthority($parts['authority']);
		$this->setPath($parts['path']);
		$this->setQuery($parts['query']);
		$this->setFragment($parts['fragment']);
	}

	/**
	 * Returns the NSS (namespace specific string). If the NSS has an known
	 * format like i.e. uuid the specific value is returned.
	 *
	 * @return string
	 */
	public function getNss()
	{
		$parts  = explode(':', $this->getPath(), 2);
		$nid    = isset($parts[0]) ? $parts[0] : '';
		$nss    = isset($parts[1]) ? $parts[1] : '';

		switch($nid)
		{
			case 'uuid':
			case 'isbn':
			case 'issn':

				return $nss;
				break;

			default:

				return $nid . ':' . $nss;
				break;
		}
	}

	public function getUrn()
	{
		$result = '';

		if(!empty($this->scheme))
		{
			$result.= $this->scheme . ':';
		}

		$result.= $this->path;

		return $result;
	}

	public static function parse($urn)
	{
		$urn = (string) $urn;
		$urn = rawurldecode($urn);
		$urn = strtolower($urn);

		$matches = array();

		preg_match_all('!^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?!', $urn, $matches);

		$parts = array(
			'scheme'    => isset($matches[2][0]) ? $matches[2][0] : null,
			'authority' => isset($matches[4][0]) ? $matches[4][0] : null,
			'path'      => isset($matches[5][0]) ? $matches[5][0] : null,
			'query'     => isset($matches[7][0]) ? $matches[7][0] : null,
			'fragment'  => isset($matches[9][0]) ? $matches[9][0] : null,
		);

		if($parts['scheme'] != 'urn')
		{
			throw new Exception('Invalid urn syntax');
		}

		return $parts;
	}

	/**
	 * Generates an urn from an array. Returns an string concatenated with
	 * an colon ':'
	 *
	 * @return string
	 */
	public static function buildUrn(array $parts)
	{
		return 'urn:' . implode(':', $parts);
	}
}

