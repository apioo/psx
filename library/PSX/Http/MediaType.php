<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Http;

use InvalidArgumentException;

/**
 * MediaType
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class MediaType
{
	protected static $topLevelMediaTypes = array(
		'application', 
		'audio', 
		'example', 
		'image', 
		'message', 
		'model', 
		'multipart', 
		'text', 
		'video'
	);

	protected $type;
	protected $subType;
	protected $quality;
	protected $parameters;

	public function __construct($type, $subType, array $parameters = array())
	{
		$this->type       = $type;
		$this->subType    = $subType;
		$this->parameters = $parameters;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getSubType()
	{
		return $this->subType;
	}

	public function getName()
	{
		return $this->type . '/' . $this->subType;
	}

	public function getQuality()
	{
		if($this->quality === null)
		{
			$this->quality = $this->_determineQuality();
		}

		return $this->quality;
	}

	public function getParameter($name)
	{
		return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
	}

	public function getParameters()
	{
		return $this->parameters;
	}

	/**
	 * Checks whether the given media type would match
	 *
	 * @return boolean
	 */
	public function match(MediaType $mediaType)
	{
		return ($this->type == '*' && $this->subType == '*') ||
			($this->type == $mediaType->getType() && $this->subType == $mediaType->getSubType()) ||
			($this->type == $mediaType->getType() && $this->subType == '*');
	}

	private function _determineQuality()
	{
		if(isset($this->parameters['q']))
		{
			$q = (float) $this->parameters['q'];

			if($q >= 0 && $q <= 1)
			{
				return $q;
			}
		}

		return 1;
	}

	public static function parse($mime)
	{
		$mime = (string) $mime;

		if(strpos($mime, ';') !== false)
		{
			$name = strstr($mime, ';', true);
			$rest = substr(strstr($mime, ';'), 1);
		}
		else
		{
			$name = $mime;
			$rest = '';
		}

		$type    = strtolower(strstr($name, '/', true));
		$subType = strtolower(rtrim(substr(strstr($name, '/'), 1)));

		if(empty($type) || empty($subType))
		{
			throw new InvalidArgumentException('Invalid media type given');
		}

		if($type != '*' && !in_array($type, self::$topLevelMediaTypes))
		{
			throw new InvalidArgumentException('Invalid media type given');
		}

		$parameters = array();

		if(!empty($rest))
		{
			$parts = explode(';', $rest);

			if(!empty($parts))
			{
				foreach($parts as $part)
				{
					$kv    = explode('=', $part, 2);
					$key   = trim($kv[0]);
					$value = isset($kv[1]) ? trim($kv[1]) : null;

					if(!empty($key))
					{
						$parameters[$key] = trim($value, '"');
					}
				}
			}
		}

		return new self($type, $subType, $parameters);
	}

	public static function parseList($mimeList)
	{
		$types  = explode(',', $mimeList);
		$result = array();

		foreach($types as $mime)
		{
			try
			{
				$result[] = self::parse(trim($mime));
			}
			catch(InvalidArgumentException $e)
			{
			}
		}

		usort($result, function($a, $b){

			if($a->getQuality() == $b->getQuality())
			{
				return 0;
			}

			return $a->getQuality() > $b->getQuality() ? -1 : 1;

		});

		return $result;
	}
}
