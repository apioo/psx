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

namespace PSX\Html\Lexer\Token;

use PSX\Html\Lexer\TokenAbstract;
use PSX\Html\Lexer\DomException;
use PSX\Html\Lexer;

/**
 * Element
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Element extends TokenAbstract
{
	const TYPE_START = 0x1; // start tag <b>
	const TYPE_END   = 0x2; // end tag </b>

	public $type;
	public $name;
	public $attr  = array();
	public $short = false; // whether its an <b /> tag or not

	public $parentNode = null;
	public $childNodes = array();

	public function __construct($type, $name, array $attr = array(), $short = false)
	{
		$this->type  = $type;
		$this->name  = $name;
		$this->attr  = $attr;
		$this->short = $short;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getAttributes()
	{
		return $this->attr;
	}

	public function getAttribute($key)
	{
		return isset($this->attr[$key]) ? htmlspecialchars_decode($this->attr[$key]) : false;
	}

	public function setAttribute($key, $value)
	{
		$this->attr[$key] = $value;
	}

	public function removeAttribute($key)
	{
		if(isset($this->attr[$key]))
		{
			unset($this->attr[$key]);
		}
	}

	public function appendChild(TokenAbstract $token)
	{
		if($token->parentNode !== null)
		{
			throw new DomException('Token is already appended to an element');
		}
		else
		{
			$token->parentNode = $this;
		}

		$this->childNodes[] = $token;
	}

	public function getChildNodes()
	{
		return $this->childNodes;
	}

	public function getElementsByTagName($name)
	{
		$elements = array();

		if(strcasecmp($this->name, $name) == 0)
		{
			$elements[] = $this;
		}

		foreach($this->childNodes as $token)
		{
			if($token instanceof Element)
			{
				$elements = array_merge($elements, $token->getElementsByTagName($name));
			}
		}

		return $elements;
	}

	public function isShort()
	{
		return $this->short;
	}

	public function __toString()
	{
		return $this->toString($this);
	}

	private function toString(TokenAbstract $token, $deep = 0)
	{
		$str = '';

		if($token instanceof Element)
		{
			if(!empty($token->childNodes))
			{
				$str.= '<' . $token->name;

				foreach($token->attr as $key => $val)
				{
					if($val !== null)
					{
						$str.= ' ' . $key . '="' . htmlspecialchars($val, ENT_COMPAT, 'UTF-8', false) . '"';
					}
					else
					{
						$str.= ' ' . $key;
					}
				}

				$str.= '>';

				foreach($token->childNodes as $t)
				{
					$str.= $this->toString($t, $deep + 1);
				}

				$str.= '</' . $token->name . '>';
			}
			else
			{
				$str.= '<' . $token->name;

				foreach($token->attr as $key => $val)
				{
					if($val !== null)
					{
						$str.= ' ' . $key . '="' . htmlspecialchars($val, ENT_COMPAT, 'UTF-8', false) . '"';
					}
					else
					{
						$str.= ' ' . $key;
					}
				}

				// specific elements can not be closed as short tag
				if(in_array($token->name, array('script', 'iframe')))
				{
					$str.= '></' . $token->name . '>';
				}
				else
				{
					$str.= ' />';
				}
			}
		}
		else if($token instanceof Text)
		{
			$str.= $token->data;
		}
		else if($token instanceof Comment)
		{
			$str.= $token->data;
		}

		return $str;
	}

	public static function parse($html)
	{
		// check empty elements
		$html = trim($html);

		if(empty($html))
		{
			return null;
		}

		// check if comment or doctype
		if($html[0] == '!' || $html[0] == '-')
		{
			return null;
		}

		// check whether is short tag
		$short = false;

		if($html[strlen($html) - 1] == '/')
		{
			$html  = trim(substr($html, 0, strlen($html) - 1));
			$short = true;
		}

		// get values
		$type = self::parseType($html);
		$name = self::parseName($html);
		$attr = Lexer::parseAttributes($html);

		// check name
		if(empty($name) || !ctype_alnum($name))
		{
			return null;
		}

		return new self($type, $name, $attr, $short);
	}

	private static function parseType(&$html)
	{
		if($html[0] == '/')
		{
			$html = trim(substr($html, 1));

			return self::TYPE_END;
		}
		else
		{
			return self::TYPE_START;
		}
	}

	private static function parseName(&$html)
	{
		$len  = strlen($html);
		$name = '';

		for($i = 0; $i < $len; $i++)
		{
			if(!in_array(ord($html[$i]), Lexer::$spaceCharacters))
			{
				$name.= $html[$i];
			}
			else
			{
				break;
			}
		}

		$html = trim(substr($html, $i));

		return strtolower($name);
	}
}

