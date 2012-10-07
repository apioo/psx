<?php
/*
 *  $Id: Webfinger.php 663 2012-10-07 16:45:52Z k42b3.x@googlemail.com $
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
 * PSX_Webfinger
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Webfinger
 * @version    $Revision: 663 $
 */
class PSX_Webfinger
{
	private $http;

	public function __construct(PSX_Http $http)
	{
		$this->http = $http;
	}

	/**
	 * Makes an GET request to the "/.well-known/host-meta" of the host. Then
	 * tries to parse the response as XRD element. The xrd must have an Host tag
	 * wich has the same host as the $url.
	 *
	 * @return PSX_Webfinger_Xrd
	 */
	public function getHostMeta(PSX_Url $url)
	{
		$url = $url->getScheme() . '://' . $url->getHost() . '/.well-known/host-meta';
		$url = new PSX_Url($url);

		$request  = new PSX_Http_GetRequest($url, array(
			'Accept'     => 'application/xrd+xml',
			'User-Agent' => __CLASS__ . ' ' . PSX_Base::VERSION,
		));
		$request->setFollowLocation(true);
		$response = $this->http->request($request);

		if($response->getCode() == 200)
		{
			$xrd = simplexml_load_string($response->getBody(), 'PSX_Webfinger_Xrd');

			if($xrd instanceof SimpleXMLElement)
			{
				// validate host
				$host = (string) $xrd->children('http://host-meta.net/xrd/1.0');

				if(strcmp($host, $url->getHost()) !== 0)
				{
					throw new PSX_Webfinger_Exception('Invalid host');
				}

				return $xrd;
			}
			else
			{
				throw new PSX_Webfinger_Exception('Could not parse xml');
			}
		}
		else
		{
			throw new PSX_Webfinger_Exception('Could not request host-meta invalid response code ' . $response->getCode());
		}
	}

	public function getLrddTemplate(PSX_Url $url)
	{
		$xrd  = $this->getHostMeta($url);
		$link = $xrd->getLinkByRel('lrdd');

		if(isset($link['template']))
		{
			return (string) $link['template'];
		}
		else
		{
			throw new PSX_Webfinger_Exception('Found no link lrdd element');
		}
	}

	public function getLrdd($uri, $lrddTemplate)
	{
		$lrddUrl  = new PSX_Url(str_replace('{uri}', urlencode($uri), $lrddTemplate));
		$request  = new PSX_Http_GetRequest($lrddUrl, array(
			'Accept'     => 'application/xml',
			'User-Agent' => __CLASS__ . ' ' . PSX_Base::VERSION,
		));
		$request->setFollowLocation(true);
		$response = $this->http->request($request);

		if($response->getCode() == 200)
		{
			$xrd = simplexml_load_string($response->getBody(), 'PSX_Webfinger_Xrd');

			// check whether api has respond with an failure
			if(isset($xrd->success) && strcasecmp($xrd->success, 'false') == 0)
			{
				$msg = isset($xrd->text) ? $xrd->text : null;

				if(!empty($msg))
				{
					throw new PSX_Webfinger_Exception($msg);
				}
				else
				{
					throw new PSX_Webfinger_Exception('Could not discover xrd');
				}
			}

			return $xrd;
		}
		else
		{
			throw new PSX_Webfinger_Exception('Invalid response code ' . $response->getCode());
		}
	}

	/**
	 * Requests the account $email xrd by using the $lrddTemplate. Checks
	 * whether the $xrd has an valid subject and returns the profile url.
	 *
	 * @return string
	 */
	public function getAcctProfile($email, $lrddTemplate)
	{
		$acct = 'acct:' . $email;
		$xrd  = $this->getLrdd($acct, $lrddTemplate);

		if(strcmp($xrd->getSubject(), $acct) !== 0)
		{
			throw new PSX_Webfinger_Exception('Invalid subject');
		}

		$profile = $xrd->getLinkHref('profile');

		if(!empty($profile))
		{
			return $profile;
		}
		else
		{
			throw new PSX_Webfinger_Exception('Could not find profile');
		}
	}
}

