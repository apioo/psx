<?php
/*
 *  $Id: Oauth2.php 594 2012-08-15 22:15:37Z k42b3.x@googlemail.com $
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

namespace PSX;

use PSX\Oauth2\AccessToken;
use PSX\Oauth2\TokenAbstract;

/**
 * PSX_Oauth2
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @see        http://tools.ietf.org/html/rfc5849
 * @category   PSX
 * @package    PSX_Oauth2
 * @version    $Revision: 594 $
 */
class Oauth2
{
	/**
	 * If you have received an access token you can use this method to get the
	 * authorization header. You can add the header to an http request to make
	 * an valid oauth2 request i.e.
	 *
	 * <code>
	 * $header = array(
	 *
	 * 	'Authorization: ' . $oauth->getAuthorizationHeader(...),
	 *
	 * );
	 * </code>
	 *
	 * @param PSX_Oauth2_AccessToken $accessToken
	 * @return string
	 */
	public function getAuthorizationHeader(AccessToken $accessToken)
	{
		return TokenAbstract::factory($accessToken)->getHeader();
	}
}

