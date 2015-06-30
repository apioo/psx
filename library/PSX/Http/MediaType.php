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
	protected $parameters;
	protected $quality;

	public function __construct($mediaType, $subType = null, array $parameters = array())
	{
		if(func_num_args() == 1)
		{
			$this->parse($mediaType);
		}
		else
		{
			$this->type       = $mediaType;
			$this->subType    = $subType;
			$this->parameters = $parameters;

			$this->parseQuality(isset($parameters['q']) ? $parameters['q'] : null);
		}
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

	public function toString()
	{
		$mediaType = $this->getName();

		if(!empty($this->parameters))
		{
			$arguments = array();
			foreach($this->parameters as $key => $value)
			{
				$arguments[] = $key . '=' . $value;
			}

			$mediaType.= '; ' . implode('; ', $arguments);
		}

		return $mediaType;
	}

	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * Checks whether the given media type would match
	 *
     * @param \PSX\Http\MediaType $mediaType
	 * @return boolean
	 */
	public function match(MediaType $mediaType)
	{
		return ($this->type == '*' && $this->subType == '*') ||
			($this->type == $mediaType->getType() && $this->subType == $mediaType->getSubType()) ||
			($this->type == $mediaType->getType() && $this->subType == '*');
	}

	protected function parse($mime)
	{
		$mime   = (string) $mime;
		$result = preg_match('/^' . self::getPattern() . '$/i', $mime, $matches);

		if(!$result)
		{
			throw new InvalidArgumentException('Invalid media type given');
		}

		$type    = isset($matches[1]) ? strtolower($matches[1]) : null;
		$subType = isset($matches[2]) ? strtolower($matches[2]) : null;

		if($type != '*' && !in_array($type, self::$topLevelMediaTypes))
		{
			throw new InvalidArgumentException('Invalid media type given');
		}

		$rest       = isset($matches[3]) ? $matches[3] : null;
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

		$this->type       = $type;
		$this->subType    = $subType;
		$this->parameters = $parameters;

		$this->parseQuality(isset($parameters['q']) ? $parameters['q'] : null);
	}

	protected function parseQuality($quality)
	{
		if(!empty($quality))
		{
			$q = (float) $quality;

			if($q >= 0 && $q <= 1)
			{
				$this->quality = $q;
				return;
			}
		}

		$this->quality = 1;
	}

	public static function parseList($mimeList)
	{
		$types  = explode(',', $mimeList);
		$result = array();

		foreach($types as $mime)
		{
			try
			{
				$result[] = new self(trim($mime));
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

	public static function getPattern()
	{
		return '([A-z]+|x-[A-z\-\_]+|\*)\/([A-z0-9\-\_\.\+]+|\*);?\s?(.*)';
	}
}
