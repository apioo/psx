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

use PSX\Html\Parse;
use PSX\Html\Parse\Element;
use PSX\Http\GetRequest;
use PSX\Http\PostRequest;
use SimpleXMLElement;

/**
 * Pingback
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Pingback
{
	public static $errorCodes = array(

		16 => 'The source URI does not exist',
		17 => 'The source URI does not contain a link to the target URI',
		32 => 'The specified target URI does not exist',
		33 => 'The specified target URI cannot be used as a target',
		48 => 'The pingback has already been registered',
		49 => 'Access denied',
		50 => 'The server could not communicate with an upstream server',

	);

	private $http;

	public function __construct(Http $http)
	{
		$this->http = $http;
	}

	/**
	 * Discovers the pingback server from the targetUri and sends an ping 
	 * request. Returns true if the pingback was successful
	 *
	 * @param string $sourceUri
	 * @param string $targetUri
	 * @return boolean
	 */
	public function send($sourceUri, $targetUri)
	{
		$request  = new GetRequest($targetUri, array(
			'User-Agent' => __CLASS__ . ' ' . Base::VERSION
		));
		$response = $this->http->request($request);

		if($response->getCode() >= 200 && $response->getCode() < 300)
		{
			$pingback = $response->getHeader('X-Pingback');

			if(empty($pingback))
			{
				if(strpos($response->getHeader('Content-Type'), 'text/html') !== false)
				{
					$pingback = self::findTag($response->getBody());
				}
			}

			if(!empty($pingback))
			{
				return $this->ping($pingback, $sourceUri, $targetUri);
			}
			else
			{
				throw new Exception('Could not find pingback server');
			}
		}
		else
		{
			throw new Exception('Invalid response code ' . $response->getCode());
		}
	}

	public function ping($server, $sourceUri, $targetUri)
	{
		$body     = $this->getRequestBody($sourceUri, $targetUri);
		$request  = new PostRequest($server, array(
			'User-Agent' => __CLASS__ . ' ' . Base::VERSION
		), $body);
		$response = $this->http->request($request);

		if($response->getCode() >= 200 && $response->getCode() < 300)
		{
			$resp = $this->parseResponseBody($response->getBody());

			if(isset($resp['faultCode']))
			{
				$faultCode = (integer) $resp['faultCode'];

				if(isset(self::$errorCodes[$faultCode]))
				{
					throw new Exception(self::$errorCodes[$faultCode]);
				}
				else
				{
					throw new Exception('An unknown error occured');
				}
			}
			else
			{
				return true;
			}
		}
		else
		{
			throw new Exception('Invalid response code ' . $response->getCode());
		}
	}

	private function getRequestBody($sourceUri, $targetUri)
	{
		$sourceUri = htmlspecialchars($sourceUri, ENT_NOQUOTES);
		$targetUri = htmlspecialchars($targetUri, ENT_NOQUOTES);

		$body = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
  <methodName>pingback.ping</methodName>
  <params>
    <param>
      <value>
        <string>{$sourceUri}</string>
      </value>
    </param>
    <param>
      <value>
        <string>{$targetUri}</string>
      </value>
    </param>
  </params>
</methodCall>
XML;

		return $body;
	}

	private function parseResponseBody($response)
	{
		$resp = simplexml_load_string($response);
		$data = array();

		if(isset($resp->fault->value->struct))
		{
			$fault = $resp->fault->value->struct;

			foreach($fault->member as $member)
			{
				$name  = isset($member->name)  ? (string) $member->name : null;
				$value = isset($member->value) ? $member->value : null;

				if(!empty($name) && $value instanceof SimpleXMLElement)
				{
					$data[$name] = $this->getDataType($value);
				}
			}
		}

		return $data;
	}

	private function getDataType(SimpleXMLElement $value)
	{
		foreach($value->children() as $el)
		{
			return (string) $el;
		}

		return null;
	}

	public static function findTag($content)
	{
		$parse   = new Parse($content);
		$element = new Element('link', array('rel' => 'pingback'));
		$href    = $parse->fetchAttrFromHead($element, 'href');

		if(empty($href))
		{
			throw new Exception('Could not discover pingback link');
		}

		return $href;
	}
}
