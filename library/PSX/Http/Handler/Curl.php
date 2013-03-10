<?php
/*
 *  $Id: Curl.php 579 2012-08-14 18:22:10Z k42b3.x@googlemail.com $
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

namespace PSX\Http\Handler;

use PSX\Http;
use PSX\Http\HandlerInterface;
use PSX\Http\RedirectException;
use PSX\Http\Request;

/**
 * The logic of following redirects is handeled in the PSX_Http class to avoid
 * that handlers have to deal with this. But because curl has an follow location
 * option wich is probably faster you can enable this in the handler. Note
 * CURLOPT_FOLLOWLOCATION throws an error if in safe mode or and open_basedir is
 * set. If you use the curl redirection the redirect will not include any
 * cookies of the store. Because of these drawbacks the option is by default
 * disabled
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Http
 * @version    $Revision: 579 $
 */
class Curl implements HandlerInterface
{
	private $lastError;
	private $request;
	private $response;

	private $hasFollowLocation;
	private $caInfo;

	public function __construct()
	{
		$this->hasFollowLocation = false;
	}

	public function setFollowLocation($followLocation)
	{
		$this->hasFollowLocation = (boolean) $followLocation;
	}

	/**
	 * Sets the name of a file holding one or more certificates to verify the
	 * peer with
	 *
	 * @param string $path
	 * @return void
	 */
	public function setCaInfo($path)
	{
		$this->caInfo = $path;
	}

	public function request(Request $request, $count = 0)
	{
		$handle = curl_init($request->getUrl()->__toString());

		curl_setopt($handle, CURLOPT_HEADER, true);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_CUSTOMREQUEST, $request->getMethod());
		curl_setopt($handle, CURLINFO_HEADER_OUT, true);


		$header = $request->getHeader();

		if(!empty($header))
		{
			$rawHeader = array();

			foreach($header as $k => $v)
			{
				$rawHeader[] = $k . ': ' . $v;
			}

			curl_setopt($handle, CURLOPT_HTTPHEADER, $rawHeader);
		}


		$body = $request->getBody();

		if(!empty($body))
		{
			curl_setopt($handle, CURLOPT_POSTFIELDS, $body);
		}


		if($request->getFollowLocation())
		{
			if($this->hasFollowLocation)
			{
				curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($handle, CURLOPT_MAXREDIRS, $request->getMaxRedirects());
			}
		}


		if($request->isSSL())
		{
			if(!empty($this->caInfo))
			{
				curl_setopt($handle, CURLOPT_CAINFO, $this->caInfo);
				curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, true);
			}
			else
			{
				curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
			}
		}


		$timeout = $request->getTimeout();

		if(!empty($timeout))
		{
			curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, $timeout);
		}


		$callback = $request->getCallback();

		if(!empty($callback))
		{
			call_user_func_array($callback, array($handle, $request));
		}


		$response = curl_exec($handle);


		if(!curl_errno($handle))
		{
			$this->lastError = false;
			$this->request   = curl_getinfo($handle, CURLINFO_HEADER_OUT);
			$this->response  = $response;
		}
		else
		{
			$this->lastError = curl_error($handle);
			$this->request   = false;
			$this->response  = false;
		}

		curl_close($handle);


		if($request->getFollowLocation() && $this->hasFollowLocation)
		{
			if($response === false)
			{
				// if the response is false max redirection is reached
				throw new RedirectException('Max redirection reached');
			}
			else
			{
				// if follow location is true all headers from each request are
				// included in the response but we only want return the last
				// headers
				$pos = strrpos($response, Http::$newLine . 'HTTP/');

				if($pos !== false)
				{
					$response = substr($response, $pos + strlen(Http::$newLine));
				}
			}
		}


		return $response;
	}

	public function getLastError()
	{
		return $this->lastError;
	}

	public function getRequest()
	{
		return $this->request;
	}

	public function getResponse()
	{
		return $this->response;
	}
}
